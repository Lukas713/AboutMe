/**
 * Javasript code for user's profile page
 */

/**
 * loads plugin for mobile phone prefixes and flags
 */
var input = document.querySelector("#phone");
var iti = window.intlTelInput(input);
window.intlTelInputGlobals.loadUtils("node_modules/intl-tel-input/build/js/utils.js");

$(document).ready(function(){

    //start with validating image input
    $("#formImg").validate({
        rules: {
            title: {
                required: true
            },
            image: {
                required: true
            }
        },
        errorClass: "is-invalid"
    });
    //start with updating info function
    updateBasicInfo();
});

/**
 * function that sens inputs on server, updates profile and display status message
 */
function updateBasicInfo(){
    $("#update").click(function(){  //when user clicks update button
        var x = document.getElementById("messageDiv");  //get div where message resides
        if(x.children.length > 0){  //if there is element
            var t = document.getElementById("message");
            x.removeChild(t);    //remove it
        }
        var formData = new FormData();
        formData.append("firstName", $("#firstName").val());
        formData.append("lastName", $("#lastName").val());
        formData.append("phone", iti.getNumber(intlTelInputUtils.numberFormat.E164));
        formData.append("update", $("#update").val());
        formData.append("profileTitle", $('#profileTitle').val());
        formData.append("file", $('#inputFile').prop('files')[0]);

        $.ajax({
            type: "post",
            url: "/profile/update",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (serverReturn) {
                var p = document.createElement("p");    //create paragraph that wil hold message
                p.setAttribute("id", "message");    //set id
                if(serverReturn === 'good job'){    //if server did good job
                    var text = document.createTextNode("Successful operation");
                    p.setAttribute("class", "text-success");    //append successful message
                    p.appendChild(text);
                    x.appendChild(p);
                }else {
                    var text = document.createTextNode("Failed operation");
                    p.setAttribute("class", "text-danger"); //append warning message
                    p.appendChild(text);
                    console.log(serverReturn);
                    x.appendChild(p);
                }
                $("#message").delay(1000);  //message fade's out
                $("#message").fadeOut(500);
            }
        });
    });
}