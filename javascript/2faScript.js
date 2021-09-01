$(window).on("load", function() {
    $('[data-toggle="tooltip"]').tooltip();
    $("input").on("input",function (){
        $(this).removeClass("is-invalid");
    })
    checkEmailSet();
});

function submit2FA(){
    let form = document.getElementById("fa-form");
    if (checkFormValidation(form)) {
        let request = new Request('api.php?type=2FA', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    $("#code").removeClass("is-invalid");
                    window.location.replace("account.html");
                }
                else {
                    if(data.message ==="first-login"){
                        showFaStatus(data.message);
                    }
                    else if(data.message ==='2FA-error'){
                        $("#code").addClass("is-invalid");
                    }
                    else{
                        showFaStatus("fa-unknown");
                    }
                }
            });
    }
}
function checkEmailSet(){
    fetch('api.php?type=emailSetCheck')
        .then(response => response.json())
        .then(data => {
            if (!data.emailSet)
                window.location.replace("index.html");
        });
}
function checkFormValidation(form){
    let inputs = $(form).find("input");
    for (let i = 0; i < inputs.length; i++) {
        if (!inputs.get(i).checkValidity())
            return false;
    }
    return true;
}
function showFaStatus(faStatus){
    let faDiv = $("#"+faStatus);
    faDiv.css("top",0);
    setTimeout(function (){faDiv.css("top","-100px");},10000)
}
