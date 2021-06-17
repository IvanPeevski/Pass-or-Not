$(document).ready(function() {
    $("#optional").hide();
    if($("#guest-button").is(':checked')){$("#fields").hide()}
    $("#guest-button").click(function(){
        $("#fields").slideUp('fast');
    });
    $("#stu").click(function(){
        $("#fields").slideDown('fast');
    });
    $('#join_lobby').click(function(){
        $('#lobbyModal').modal('toggle')
    })
    $('#create_lobby').click(function(){
        $('#createModal').modal('toggle')
    })
})
function ContestStart(){
    alert('Създаването на лоби не е налично в момента')
}
