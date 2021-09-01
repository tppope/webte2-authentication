$(window).on("load", function() {
    $('[data-toggle="tooltip"]').tooltip();
    showUserHistoryInfo();
});
function showUserHistoryInfo(){
    fetch('api.php?type=acc')
        .then(response => response.json())
        .then(data => {
            if (!data.error){
                if (data.status === "success") {
                    printUserName(data.user);
                    showHistory();
                    showStats();
                }
                else{
                    printFirstLogin();
                    window.location.replace("index.html");
                }
            }
        });
}

function printUserName(user){
    $('#section-h2').text(user.name + " " +user.surname+ " - História prihlásení");
}
function printFirstLogin(){
    $('#section-h2').text("Najprv sa musíte prihlásiť");
}
function showHistory(){
    fetch('api.php?type=ownHistory')
        .then(response => response.json())
        .then(data => {
            if (!data.error){
                printHistory(data.listHistory);
            }
        });
}
function printHistory(listHistory){
    let tbody = $("#table-history").find("tbody");
    for (let ownHistoryElement of listHistory) {
        tbody.append(createHistoryRow(ownHistoryElement.timestamp));
    }
}
function createHistoryRow(timestamp){
    let tr = document.createElement("tr");
    let td = document.createElement("td");
    tr.append(td);
    $(td).text(timestamp)
    return tr;
}
function showStats(){
    fetch('api.php?type=loginStats')
        .then(response => response.json())
        .then(data => {
            if (!data.error){
                printStats(data.loginStats);
            }
        });
}
function printStats(loginStats){
    let tbody = $("#table-history-stats").find("tbody");
    for (let loginStat of loginStats) {
        tbody.append(createLoginStatsRow(loginStat.type,loginStat.typeCount));
    }
}
function createLoginStatsRow(type,typeCount){
    let tr = document.createElement("tr");
    let td1 = document.createElement("td");
    let td2 = document.createElement("td");
    tr.append(td1,td2);
    $(td1).text((type === 'own'? "vlastné" : type));
    $(td2).text(typeCount);
    return tr;
}
