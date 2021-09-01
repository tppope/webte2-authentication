$(window).on("load", function() {
    $('[data-toggle="tooltip"]').tooltip();
    $("#logout").hide();
    $("#undo").hide();
    $("#button-history").hide();
    showUserAccount();
});

function showUserAccount(){
    fetch('api.php?type=acc')
        .then(response => response.json())
        .then(data => {
            if (!data.error){
                if (data.status === "success"){
                    printUserInfo(data.user);
                    if (localStorage.getItem("noLogin")!=null)
                        showLogStatus();
                    localStorage.removeItem("noLogin");
                }
                else{
                    printFirstLogin();
                    window.location.replace("index.html");
                }

            }
        });

}

function printUserInfo(user){
    $("#logout").show();
    $('#section-h2').text(user.name + " " +user.surname);
    $("#email").html("<strong>Email: </strong>"+user.email);
    $("#reg-date").html("<strong>Typ prihlásenia: </strong>"+(user.loginType ==="own" ? "vlastná" : user.loginType));
    $("#button-history").show();
}
function printFirstLogin(){
    $("#undo").show();
    $('#section-h2').text("Najprv sa musíte prihlásiť");
}
function logout(){
    fetch('api.php?type=logout')
        .then(response => response.json())
        .then(data => {
            if (!data.error){
                localStorage.setItem("status", "logout-success");
                window.location.replace('index.html')
            }
        });


}
function showLogStatus(){
    let logDiv = $("#log-success");
    logDiv.css("top",0);
    setTimeout(function (){logDiv.css("top","-100px");},3000)
}
