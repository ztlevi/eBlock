// declare Class View_HomePage

var View_HomePage = function() {

    var getDomainFromUrl = function (url) {
        var host = "null";
        if (typeof url == "undefined" || null == url)
            url = window.location.href;
        var regex = /.*\:\/\/([^\/]*).*/;
        var match = url.match(regex);
        if (typeof match != "undefined" && null != match)
            host = match[1];
        return host;
    }
    var MessageSent = function (tabId, tab, data, textStatus, jqXHR) {
        console.log("Success Message");
        console.log(tabId);
        console.log(tab);
        console.log(data);
        console.log(textStatus);
        console.log(jqXHR);
        if(data == "allow"){
        }
        else if(data == "deny"){
            chrome.tabs.remove(tabId);
            alert("This page is not allowed");
        }
    }

    var MessageRejected = function (jqXHR, textStatus, errorThrown) {
        console.log("Error Message:");
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    }
    this.checkForValidUrl = function (tabId, changeInfo, tab) {
        if (changeInfo.url == undefined || changeInfo.url.match("chrome://") || changeInfo.url.match("www.google.com"))return;
        else {
            var url = tab.url;
            console.log("tab.url" + tab.url);
            if (url != undefined) {
                var domain = getDomainFromUrl(tab.url);
                $.ajax({
                    async: false,
                    url: "http://localhost/E-Guard/E-Guard-Server/index.php",
                    cache: false,
                    type: "POST",
                    data: JSON.stringify({action: "request", url: domain}),
                    dataType: "json"
                }).done(function (data, textStatus, jqXHR) {
                    MessageSent(tabId, tab, data, textStatus, jqXHR);
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    MessageRejected(jqXHR, textStatus, errorThrown);
                })
            }
        }
    }
}

//declare Timer CLass
var Timer = (function(){
    var instance;
    function init(localStorage){
    // Interval (in seconds) to update timer
        var UPDATE_INTERVAL = 3;
        var limitedTime = parseInt(localStorage["timeLimitation"]);
        // Add sites which are not in the top threshold sites to "other" category
        // WARNING: Setting the threshold too low will schew the data set
        // so that it will favor sites that already have a lot of time but
        // trash the ones that are visited frequently for short periods of time
        var combineEntries = function (threshold) {
            var domains = JSON.parse(localStorage["domains"]);
            var other = JSON.parse(localStorage["other"]);
            // Don't do anything if there are less than threshold domains
            if (Object.keys(domains).length <= threshold) {
                return;
            }
            // Sort the domains by decreasing "all" time
            var data = [];
            for (var domain in domains) {
                var domain_data = JSON.parse(localStorage[domain]);
                data.push({
                    domain: domain,
                    all: domain_data.all
                });
            }
            data.sort(function (a, b) {
                return b.all - a.all;
            });
            // Delete data after top threshold and add it to other
            for (var i = threshold; i < data.length; i++) {
                other.all += data[i].all;
                var domain = data[i].domain;
                delete localStorage[domain];
                delete domains[domain];
            }
            localStorage["other"] = JSON.stringify(other);
            localStorage["domains"] = JSON.stringify(domains);
        }

        // Check to make sure data is kept for the same day
        var checkDate = function () {
            var todayStr = new Date().toLocaleDateString();
            var saved_day = localStorage["date"];
            if (saved_day !== todayStr) {
                // Reset today's data
                var domains = JSON.parse(localStorage["domains"]);
                for (var domain in domains) {
                    var domain_data = JSON.parse(localStorage[domain]);
                    domain_data.today = 0;
                    localStorage[domain] = JSON.stringify(domain_data);
                }
                // Reset total for today
                var total = JSON.parse(localStorage["total"]);
                total.today = 0;
                localStorage["total"] = JSON.stringify(total);
                // Combine entries that are not part of top 500 sites
                combineEntries(500);
                // Keep track of number of days web timer has been used
                localStorage["num_days"] = parseInt(localStorage["num_days"]) + 1;
                // Update date
                localStorage["date"] = todayStr;
            }
        }

        // Extract the domain from the url
        // e.g. http://google.com/ -> google.com
        var extractDomain = function (url) {
            var re = /:\/\/(www\.)?(.+?)\//;
            return url.match(re)[2];
        }

        var inBlacklist = function (url) {
            if (!url.match(/^http/)) {
                return true;
            }
            var blacklist = JSON.parse(localStorage["blacklist"]);
            for (var i = 0; i < blacklist.length; i++) {
                if (url.match(blacklist[i])) {
                    return true;
                }
            }
            return false;
        }
        return {
            getUPDATE_INTERVAL : function () {
                return UPDATE_INTERVAL;
            },

        //set Time limitation
            setTimer : function(time){
                limitedTime = time*60*60;

            },

            getTimer : function(){
              return limitedTime;
            },

        // Set default settings
            setDefaults : function () {
                // Set blacklist
                if (!localStorage["blacklist"]) {
                    localStorage["blacklist"] = JSON.stringify(["example.com"]);
                }
                // Set number of days Web Timer has been used
                if (!localStorage["num_days"]) {
                    localStorage["num_days"] = 1;
                }
                // Set date
                if (!localStorage["date"]) {
                    localStorage["date"] = new Date().toLocaleDateString();
                }
                // Set domains seen before
                if (!localStorage["domains"]) {
                    localStorage["domains"] = JSON.stringify({});
                }
                // Set total time spent
                if (!localStorage["total"]) {
                    localStorage["total"] = JSON.stringify({
                        today: 0,
                        all: 0
                    });
                }
                if (!localStorage["timeLimitation"]){
                    localStorage["timeLimitation"] = 7200;
                }
                // Limit how many sites the chart shows
                if (!localStorage["chart_limit"]) {
                    localStorage["chart_limit"] = 7;
                }
                // Set "other" category
                // NOTE: other.today is not currently used
                if (!localStorage["other"]) {
                    localStorage["other"] = JSON.stringify({
                        today: 0,
                        all: 0
                    });
                }
            },
            // Update the data
            updateData : function () {
                // Only count time if system has not been idle for 30 seconds
                chrome.idle.queryState(30, function (state) {
                    if (state === "active") {
                        // Select single active tab from focused window
                        chrome.tabs.query({'lastFocusedWindow': true, 'active': true}, function (tabs) {
                            if (tabs.length === 0) {
                                return;
                            }
                            var tab = tabs[0];
                            // Make sure 'today' is up-to-date
                            checkDate();
                            if (!inBlacklist(tab.url)) {
                                var domain = extractDomain(tab.url);
                                // Add domain to domain list if not already present
                                var domains = JSON.parse(localStorage["domains"]);
                                if (!(domain in domains)) {
                                    domains[domain] = 1;
                                    localStorage["domains"] = JSON.stringify(domains);
                                }
                                var domain_data;
                                if (localStorage[domain]) {
                                    domain_data = JSON.parse(localStorage[domain]);
                                } else {
                                    domain_data = {
                                        today: 0,
                                        all: 0
                                    };
                                }
                                domain_data.today += UPDATE_INTERVAL;
                                domain_data.all += UPDATE_INTERVAL;
                                localStorage[domain] = JSON.stringify(domain_data);
                                // Update total time
                                var total = JSON.parse(localStorage["total"]);
                                total.today += UPDATE_INTERVAL;
                                if (total.today > parseInt(window.localStorage["timeLimitation"])){
                                    chrome.windows.getCurrent(function(window){
                                        alert("Time is over! Please contact the administrator to modify limited time.");
                                        chrome.windows.remove(window.id);
                                    });
                                }
                                total.all += UPDATE_INTERVAL;
                                localStorage["total"] = JSON.stringify(total);
                                // Update badge with number of minutes spent on
                                // current site
                                var num_min = Math.floor(domain_data.today / 60).toString();
                                if (num_min.length < 4) {
                                    num_min += "m";
                                }
                                chrome.browserAction.setBadgeText({
                                    text: num_min
                                });
                            } else {
                                // Clear badge
                                chrome.browserAction.setBadgeText({
                                    text: ""
                                });
                            }
                        });
                    }
                });
            }
        }
    }
    return{
    getInstance: function(localStorage) {

        if ( !instance ) {
            instance = init(localStorage);
        }

        return instance;
    }
    }
})();


// Main function
// Types to view data
var TYPE = {
    today: "today",
    average: "average",
    all: "all"
};
// Current viewing mode
var mode = TYPE.today;
var timer = Timer.getInstance(this.localStorage);
var UPDATE_INTERVAL = timer.getUPDATE_INTERVAL();
timer.setDefaults();
// Update timer data every UPDATE_INTERVAL seconds
setInterval(timer.updateData, UPDATE_INTERVAL * 1000);
viewHomePage = new View_HomePage();
chrome.tabs.onUpdated.addListener(viewHomePage.checkForValidUrl);





