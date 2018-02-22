var statemarque = 0;

function changeColor(){

    var myDiv = document.getElementById('searchArtist');

    if(statemarque==0){

        myDiv.style.display = "block";
        statemarque = 1;
    } else {

        myDiv.style.display ="none";
        statemarque = 0;
    }
}