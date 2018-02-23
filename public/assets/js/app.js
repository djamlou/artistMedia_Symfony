var statemarque = 0;

function login(){

    var myDiv = document.getElementById('login');

    if(statemarque==0){

        myDiv.style.display = "block";
        statemarque = 1;
    } else {

        myDiv.style.display ="none";
        statemarque = 0;
    }
}