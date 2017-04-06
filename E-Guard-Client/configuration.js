/**
 * Created by Ting Zhou on 4/6/2017.
 */
function initialization(){
    $.ajax({
        async: false,
        url: "http://localhost/E-Guard/E-Guard-Server/index.php",
        cache: false,
        type: "POST",
        data: JSON.stringify({action: "configini"}),
        dataType: "json"
    }).done(function (data, textStatus, jqXHR) {
        initializeElement( data, textStatus, jqXHR);
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("initialization failed!!")
    })
}
function initializeElement(data,textStatus,jqXHR){
    var blockedCategories = data.block_category;
    for (var numOfAbandonedCategories = 0; numOfAbandonedCategories < blockedCategories.length; numOfAbandonedCategories++){
        document.getElementById(blockedCategories[numOfAbandonedCategories]).checked = true;
    }
    document.getElementById("time").value = window.localStorage["timeLimitation"]/3600;
    document.getElementById("whitelist").value = data.white_list;
    document.getElementById("blacklist").value = data.black_list;

}

function submit(){
    var configuration = {};
    configuration.blockedCategories = [];
    $('input:checkbox:checked').map(function() {
        configuration.blockedCategories.push(this.id);
    }).get();
    configuration.timer = parseInt(document.getElementById("time").value);
    configuration.whitelist = document.getElementById("whitelist").value+",";
    configuration.blacklist = document.getElementById("blacklist").value+",";
    configuration.website = document.getElementById("website").value;
    var select = document.getElementById("category");
    configuration.category = select.options[select.selectedIndex].value;
    configuration.action = "configsave";
    var data = JSON.stringify(configuration);
    window.localStorage["timeLimitation"] = (configuration.timer*3600).toString();
    $.ajax({
        async: false,
        url: "http://localhost/E-Guard/E-Guard-Server/index.php",
        cache: false,
        type: "POST",
        data: data,
        dataType: "json"
    }).done(function (data, textStatus, jqXHR) {
        alert("Successfully saved!");
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert("saving failed!!")
    })
}

document.addEventListener('DOMContentLoaded', initialization);
document.getElementById('saveForm').addEventListener('click', submit);
