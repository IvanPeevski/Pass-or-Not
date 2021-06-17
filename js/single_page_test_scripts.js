const urlParams = new URLSearchParams(window.location.search);
const pin = urlParams.get('testId');
const name = urlParams.get('name');
const _class = urlParams.get('class');
const division = urlParams.get('division');
const guest = urlParams.get('guest');
var response_pin;
var question_content='test';
var intverval;

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}
function startTimer(duration, display) {
    var start = Date.now(),
        diff,
        minutes,
        seconds;

    function timer() {

        diff = duration - (((Date.now() - start) / 1000) | 0);
        if (diff == 0) {
            Redirect()
        }
        minutes = (diff / 60) | 0;
        seconds = (diff % 60) | 0;

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = minutes + ":" + seconds;
        if (diff <= 0) {
            start = Date.now() + 1000;
        }
    };
    timer();
    interval = setInterval(timer, 1000);
}
function Redirect(){
    $.ajax({
        type: "POST",
        url: "/actions/crud/finish_response.php",
        data: {
            response_pin,
        },
        success: function(){
            window.location.href="/viewscore.php?viewscore="+response_pin;
        }
    })
}
function Begin(){
    $.ajax({
        type: "POST",
        url: "/actions/crud/create_response.php",
        data: {
            pin: pin,
            name: name,
            class: _class,
            division: division,
            guest: guest
        },
        success: function(pin){
            response_pin= pin;
            GetQuestion()
            document.getElementById('submit').value='Продължи';
            document.getElementById('submit').onclick= SendResponse;
            let seconds = document.querySelector('#time').textContent.trim();
            if (seconds != '∞') {
                seconds = 60 * parseInt(seconds.substr(0, seconds.indexOf(':'))) + parseInt(seconds.substr(seconds.indexOf(':') + 1))
                let display = document.querySelector('#time');
                startTimer(seconds, display);
            }
        },
        error: function(err){
            console.log(err)
        }
    })
}
function GetQuestion(){
    $.ajax({
        type: "POST",
        url: "/actions/info/get_question.php",
        dataType: 'json',
        data: {
            response_pin,
            test_pin: pin
        },
        success: function(obj){
            LoadQuestion(obj.response)
            if(obj.info == 'last'){
                document.getElementById('submit').value='Завърши';
            }
        },
        error: function(err){console.log(err.responseText)}
    })
}
function LoadQuestion(question_content){
    let question_body = '';
    if(question_content.type=="radio" || question_content.type=="checkbox"){
        for(let i=0; i<question_content.answers.length; i++){
            let answer = question_content.answers[i];
            let answer_file = '';
            if(answer.hasOwnProperty('file')){
                if(answer.file.name.match(/.(jpg|jpeg|png|gif)$/i)){
                    answer_file = `<img src="${answer.file.src}" class="zoom-image" alt="${answer.file.name}"/>`
                }
                else{
                    answer_file = `<br><a href="${answer.file.src}" download>${answer.file.name}</a>`
                }
            }
            question_body+=`
            <div class="answerBody">
                <input type="${question_content.type}" class="answer" name="answer" id="answer-${answer.id}" value="${answer.id}" style="visibility:hidden">
                <label for="answer-${answer.id}" class="option">${answer.text}${answer_file}</label>
            </div>`;
        }
    }
    else if(question_content.type=="text"){
        question_body=`<textarea onkeyup="auto_grow(this)" class="question" placeholder="Отговор" 
        id="answer" style="margin:auto"></textarea>`;
    }
    else if(question_content.type=="file"){
        question_body=`<div class="custom-file">
        <input type="file" class="custom-file-input" name="answer" 
        onchange="this.parentNode.children[1].innerText=this.files[0].name">
        <label class="custom-file-label" for="answer">Не е избран файл</label>
        </div>`;
    }
    let title_file = '';
    if(question_content.hasOwnProperty('file')){
        if(question_content.file.name.match(/.(jpg|jpeg|png|gif)$/i)){
            title_file = `<img src="${question_content.file.src}" class="zoom-image" alt="${question_content.file.name}"/>`
        }
        else{
            title_file = `<a href="${question_content.file.src}" download>${question_content.file.name}</a>`
        }
    }
    document.querySelector('.box-body').innerHTML = 
    `<form id="form">
        <h2 id="${question_content.id}" class="question-title">${question_content.title}</h2>
        ${title_file}
        <div class="question-body inputGroup" type="${question_content.type}">
            ${question_body}
        </div>
        <div class="footer">
            <div style="color: gray; font-size: 16px; text-align:center;">
                    ${question_content.points} точки${question_content.mandatory?' - Задължителен':''}
            </div>
        </div>
    </form>`;
    let current_question = 1*document.getElementById('progressbar').getAttribute('aria-valuenow')+1;
    document.getElementById('question_count').textContent = current_question;
    document.getElementById('progressbar').setAttribute('aria-valuenow', current_question);
    document.getElementById('progressbar').setAttribute('style', `width:${current_question/document.getElementById('progressbar').getAttribute('aria-valuemax')*100}%`)



}
function SendResponse(){
    let question_id = document.getElementsByClassName('question-title')[0].id;
    let type = document.getElementsByClassName('question-body')[0].getAttribute('type');
    if(type=="file"){
        var formData = new FormData(document.getElementById('form'));
        formData.append('response_pin', response_pin)
        formData.append('question_id', question_id)
        $.ajax({
            type: "POST",
            url: "/actions/crud/send_answer.php",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            dataType: 'json',
            success: function(obj){
                if(obj.msg=='finish'){
                    Finish(obj.response)
                }
                else if(obj.msg=='redirect'){
                    Redirect()
                }
                else{
                    GetQuestion()
                }
            },
            error: function(msg){
                console.log(msg)
            }
        })
    }
    else{
        if(type=='radio'){
            answer = $("input[name=answer]:checked").val()
        }
        else if(type=="checkbox"){
            answer = []
            let checkboxes = document.querySelectorAll('input[type=checkbox]:checked')
    
            for (let i = 0; i < checkboxes.length; i++) {
                answer.push(checkboxes[i].value)
            }
        }
        else if(type=="text"){
            answer = document.getElementById('answer').value
        }
        $.ajax({
            type: "POST",
            url: "/actions/crud/send_answer.php",
            dataType: 'json',
            data:{
                response_pin,
                question_id,
                answer
            },
            success: function(obj){
                if(obj.msg=='finish'){
                    Finish(obj.response)
                }
                else if(obj.msg=='redirect'){
                    Redirect()
                }
                else{
                    GetQuestion()
                }
            },
            error: function(msg){
                console.log(msg)
            }
        })
    }
}
function Finish(response){
    document.querySelector('.box-body').innerHTML = 
    `
    <div class="score-container">
        <div class="score-text">
            <p>Вашата оценка е <strong>${response.grade_text}(${response.grade})</strong></p>
            <p>
            Получени точки: 
            <strong><span style="color:green">${response.points}</span> / ${response.maxpoints}</strong><br>
            <strong><span style="color:orange">${response.undecided}</span> недооценени</strong>
            </p>
            <p>Изпратен в <strong>${response.date}</strong></p>
            <a href="/viewscore.php?viewscore=${response_pin}">Прегледай подробен резултат+</а>
        </div>
        <div><canvas id="canvas" width="325" height="325"></canvas></div>
    </div>
    `
    if (typeof interval !== 'undefined') clearInterval(interval);
    drawChart(150, 150, 125, 25, response.maxpoints, response.points, response.undecided, response.grade_text, response.grade)
    document.getElementById('submit').style.display = 'none'
}