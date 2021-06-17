var questionIndex = 0;
var answerIndex = 0;
var selectedType = '';
var import_test;

//Return unique index of element
function getElementIndex(element) {
    return element.id.substr(element.id.indexOf('-') + 1);
}
//Toggle tooltips, Ready tags, Load content
window.onload = function(){
    $('[data-toggle="tooltip"]').tooltip()
    readyTags()
    let testId = new URLSearchParams(window.location.search).get('testId')
    if(testId){
        $.ajax({
            type: 'POST',  
            url: '/actions/info/get_test.php',
            dataType: 'json',
            data: {
                testId
            },
            success: function(test){
                test.questions.forEach(question=> ImportQuestion(question))
                document.getElementById('title').value = test.name
                document.getElementById('unlocked').checked = parseInt(test.settings.unlocked)
                document.getElementById('public').checked = parseInt(test.settings.public)
                document.getElementById('grading').value = test.settings.grading
                document.getElementById('one_per_one').checked = parseInt(test.settings.one_per_one)
                document.getElementById('randomize_questions').checked = parseInt(test.settings.randomize_questions)
                document.getElementById('randomize_answers').checked = parseInt(test.settings.randomize_answers)
                document.getElementById('question_limit').value = parseInt(test.settings.question_limit)
                document.getElementById('time_limit').value = parseInt(test.settings.time_limit)
                document.getElementById('individual_time').checked = parseInt(test.settings.individual_time)
                document.getElementById('require_profile').checked = parseInt(test.settings.require_profile)
                document.getElementById('allow_anonymous').checked = parseInt(test.settings.allow_anonymous)
                document.getElementById('limit_response').checked = parseInt(test.settings.limit_response)
                document.getElementById('check_points').checked = parseInt(test.settings.check_points)
                document.getElementById('check_answers').checked = parseInt(test.settings.check_answers)
                document.getElementById('limit_check').checked = parseInt(test.settings.limit_check)
                document.getElementById('team_limit').value = test.settings.team_limit
                addTags(test.settings.tags.split(','))
            },
            error: function(msg){
                console.log(msg.responseText)
            }
        })
    }
}
//Add auto-grow to textarea element
function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}

//Insert <b> tags in question-title 
function InsertBold(){
    document.getElementById('question-title').value+='<b></b>';
    document.getElementById('question-title').focus();
    document.getElementById('question-title').selectionEnd-=4;
}

//Insert <i> tags in question-title 
function InsertItalic(){
    document.getElementById('question-title').value+='<i></i>';
    document.getElementById('question-title').focus();
    document.getElementById('question-title').selectionEnd-=4;
}

//Insert <u> tags in question-title 
function InsertUnderline(){
    document.getElementById('question-title').value+='<u></u>';
    document.getElementById('question-title').focus();
    document.getElementById('question-title').selectionEnd-=4;
}

//Load and preview attached file
function LoadFile(inputField) {
    let attachId = getElementIndex(inputField)
    let preview = document.getElementById("file-" + attachId);
    let file = document.getElementById('inputFile-' + attachId).files[0];
    let reader = new FileReader();
    reader.addEventListener("load", function () {
        preview.src = reader.result;
    }, false);

    if (file) {
        reader.readAsDataURL(file);
         
        if (file.type.substr(0, 5) == 'image') {
            document.getElementById("file-" + attachId).setAttribute('type', 'image');
        } else {
            document.getElementById("file-" + attachId).setAttribute('type', 'application');
        }

        document.getElementById("file-" + attachId).setAttribute('name', file.name);
        document.getElementById("inputFileName-" + attachId).textContent = file.name;
    }
}

//Remove file
function RemoveFile(index) {
    document.getElementById('file-' + index).removeAttribute('src');
    document.getElementById('file-' + index).removeAttribute('type');
    document.getElementById('file-' + index).removeAttribute('name');
    document.getElementById('inputFileName-' + index).textContent = "Няма избран файл";
}

//return innerHTML of answer element
function GenerateAnswer(index, type) {
    return `
    <div class="answer input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <input type="${type}" disabled style="margin-bottom:0">
            </div>
        </div> 
        <input type="text" name="answer" class="form-control">
        <div class="input-group-append">
            <div class="input-group-text">
                <button id="filemenu-${index}" class="btn" type="button" data-toggle="collapse" data-target="#fileattach${index}" 
                aria-expanded="false" aria-controls="fileattach${index}" style="padding:0">
                    <svg width="25px" viewBox="0 0 16 16" class="bi bi-image" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M14.002 2h-12a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm-12-1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12z"/>
                        <path d="M10.648 7.646a.5.5 0 0 1 .577-.093L15.002 9.5V14h-14v-2l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71z"/>
                        <path fill-rule="evenodd" d="M4.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                    </svg>
                </button> 
            </div>
            <div class="btn-group-toggle" data-toggle="buttons">
                <label class="btn switch" data-toggle="tooltip" data-placement="left" title="Избери като правилен">
                    <input type="checkbox" autocomplete="off"> T
                </label>
            </div>
            <div class="input-group-text">
               <button type="button" class="close remove" onclick="RemoveAnswer(this)" id="remove-${index}" style="float:none" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <div class="collapse" id="fileattach${index}">
        <div class="card card-body">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputFile-${index}" onchange="LoadFile(this)">
                <label class="custom-file-label" for="inputFile-${index}" id="inputFileName-${index}">Няма избран файл</label>
            </div>
            <img id="file-${index}" width="130px" height="130px">
            <button type="button" id="btn-${index}" class="btn btn-warning" onclick="RemoveFile(getElementIndex(this))">Премахни файл</button>
        </div>
    </div>`;
}

//Add answer element
function AddAnswer(type, answer_obj=false) {
    answerIndex++;
    if(type=='text'){
        GenerateTextAnswer(answerIndex)
        if(answer_obj){
            document.querySelector(`#answer-${answerIndex} .form-control`).value = answer_obj.text;
            document.querySelector(`#answer-${answerIndex} div .points-control`).value = answer_obj.points;
        }
    }
    else{
        let tag = document.createElement('span')
        tag.setAttribute("id", "answer-" + answerIndex)
        tag.innerHTML = GenerateAnswer(answerIndex, type)
        if(answer_obj){
            tag.querySelector('.answer .form-control').value=answer_obj.text.replace(/&lt;/g, '<').replace(/&gt;/g, '>')
            if(answer_obj.correct){
                tag.querySelector('.answer .input-group-append .btn-group-toggle .switch input').setAttribute('checked', '')
                tag.querySelector('.answer .input-group-append .btn-group-toggle .switch').classList.add('active')
            }
            if(answer_obj.hasOwnProperty('file')){
                tag.querySelector('#file-'+answerIndex).setAttribute('type', answer_obj.file.type)
                tag.querySelector('#file-'+answerIndex).setAttribute('name', answer_obj.file.name)
                tag.querySelector('#file-'+answerIndex).setAttribute('src', answer_obj.file.src)
                tag.querySelector('#inputFileName-'+answerIndex).textContent=answer_obj.file.name
            }
        }
        document.getElementById("answers").appendChild(tag)
        $('.switch[data-toggle="tooltip"]').tooltip({ trigger: "hover" })
    }
}

function GenerateTextAnswer(index){
    let wrapper = document.createElement('div')
    wrapper.classList.add(...['answer', 'input-group', 'text-answer'])
    wrapper.id = 'answer-'+index
    wrapper.innerHTML = `
    <input type="text" name="answer" class="form-control" placeholder="Отговор">
    <div class="input-group-append">
        <input type="number" aria-label="Точки" placeholder="Точки" class="points-control">
        <div class="input-group-text">
            <button type="button" class="close remove" onclick="RemoveAnswer(this)" id="delete-${index}" style="float:none" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>`

    document.getElementById("answers").appendChild(wrapper)
    $('.switch[data-toggle="tooltip"]').tooltip({ trigger: "hover" })
}
//Display 'Add answer' button
function DisplayInsertBtn() {
    document.querySelector("#answerButton").style.display="";
}

//Radio
function DisplayRadioButtons() {
    if(selectedType=='checkbox'){
        document.querySelectorAll('.answer > .input-group-prepend > .input-group-text > input[type=checkbox]').forEach(el=>el.type='radio')
    }
    else if(selectedType!='radio'){
        document.getElementById('answers').innerHTML = '';
        AddAnswer('radio');
        DisplayInsertBtn();
    }
    else{
        AddAnswer('radio');
    }
    selectedType = 'radio'
    document.getElementById('text-settings').style.display='none';
}


//Display CheckBox input
function DisplayCheckBox() {
    if(selectedType=='radio'){
        document.querySelectorAll('.answer > .input-group-prepend > .input-group-text > input[type=radio]').forEach(el=>el.type='checkbox')
    }
    else if(selectedType!='checkbox'){
        document.getElementById('answers').innerHTML = '';
        AddAnswer('checkbox');
        DisplayInsertBtn();
    }
    else{
        AddAnswer('checkbox');
    }
    selectedType = 'checkbox'
    document.getElementById('text-settings').style.display='none';
}

//Display Text input
function DisplayTextInput() {
    DisplayInsertBtn();
    document.getElementById('answers').innerHTML = 
    `<div class="answer-text-field" style="margin-bottom:15px;"><input type="text" class="form-field" placeholder="Свободен отговор, въведен от попълващия..." readonly/>
    <span style="color:gray;">Дори отговорът на ученика да не съвпада с изброените, ще имате възмножност да го оцените ръчно!</span></div>`
    if(selectedType!='text'){
        AddAnswer('text')
    }
    selectedType = 'text'
    document.getElementById('text-settings').style.display='block';
}

//Display File input
function DisplayFileInput() {
    selectedType = 'file'
    document.querySelector("#answerButton").style.display="none"
    document.getElementById('answers').innerHTML = `
    <div class="custom-file">
        <input type="file" class="custom-file-input" id="disabledFileInput" disabled>
        <label class="custom-file-label" for="disabledFileInput">Файл, избран от попълващия</label>
    </div>
    `;
    document.getElementById('text-settings').style.display='none';
}

//Receive remove btn element
//Remove answer
function RemoveAnswer(remove_btn) {
    document.getElementById('answer-'+getElementIndex(remove_btn)).parentElement
    .removeChild(document.getElementById('answer-'+getElementIndex(remove_btn)))
}

//Open Modal
function OpenQuestionEditor(editing=false, index=undefined) {
    if(editing){
        document.getElementById("ModalLabel").textContent = "Редактиране на въпрос";
        ClearModal()
        FillModal(QuestionObject(index))
    }
    else{
        document.getElementById("ModalLabel").textContent = "Добавяне на въпрос";
        ClearModal()
    }

    $('#QuestionEditor').modal('show')
    document.getElementById('import-btn').onclick =  function(){ImportQuestion(ConvertContent(), editing, index)}
}

//Clear all content from question modal
function ClearModal(){
    document.getElementById('question-title').value = ''
    RemoveFile(0)
    Array.from(document.querySelectorAll(`.choice-btn`)).forEach(btn=>btn.classList.remove('active'))
    $(`input:radio[name=question-type]:checked`).prop("checked", false)
    document.getElementById('answers').innerHTML=''
    answerIndex = 0;
}

//Receive question object
//Fill modal content
function FillModal(question){
    document.getElementById('question-title').value = question.title.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/<br>/g, '\n')
    if(question.hasOwnProperty('file')){
        document.getElementById('file-0').setAttribute('type',  question.file.type)
        document.getElementById('file-0').setAttribute('name',  question.file.name)
        document.getElementById('file-0').setAttribute('src',  question.file.src)
        document.getElementById('inputFileName-0').textContent = question.file.name
    }
    selectedType = question.type
    if(selectedType == 'text'){
        DisplayTextInput()
        for(let i=0; i<question.answers.length; i++){
            AddAnswer(question.type, question.answers[i])
        }
        document.getElementById('case-sensitive').checked = question.case_sensitive;
        document.getElementById('character-limit').value = question.character_limit;
        document.getElementById('print-lines').value = question.print_lines;
    }
    else if(selectedType== 'file'){
        DisplayFileInput()
    }
    else if(selectedType == 'radio' || selectedType == 'checkbox'){
        for(let i=0; i<question.answers.length; i++){
            AddAnswer(question.type, question.answers[i])
        }
        document.getElementById(selectedType+'-choice').classList.add('active')
        DisplayInsertBtn();
    }
}

//Receive question object, editing(boolean), index(int)
//Create or Update question
function ImportQuestion(question, editing=false, index=undefined){
    if(!editing){
        index = questionIndex
        var container = document.createElement('div')
        container.innerHTML =
        `<div class="d-flex wrap question-container">
            <div class="col-10 displayed-title">
                <h3 id="question-title-${index}"></h3>
            </div>
            <div class="question-btn-box">
                <button type="button" class="btn edit" data-toggle="tooltip" data-placement="bottom" id="edit-${index}" 
                title="Редктирай"  onclick="OpenQuestionEditor(true, getElementIndex(this))">
                    <img src="images/edit.svg" class="image-sm">
                </button>
                <button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" id="delete-${index}" 
                title="Изтрий" onclick="RemoveQuestion(getElementIndex(this))">
                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
                </button>
                <button type="button" class="btn duplicate" data-toggle="tooltip" data-placement="bottom" id="duplicate-${index}" 
                title="Създай копие" onclick="DuplicateQuestion(getElementIndex(this))">
                    <img src="images/file_copy.svg" class="image-sm">
                </button>
                <div class="break"></div>
                <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="bottom" id="moveup-${index}" 
                title="Премести нагоре" onclick="MoveUp(getElementIndex(this))">
                    <img src="images/arrow_upward.svg" class="image-sm">
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="bottom" id="movedown-${index}" 
                title="Премести надолу" onclick="MoveDown(getElementIndex(this))">
                    <img src="images/arrow_downward.svg" class="image-sm">
                </button>     
            </div>
        </div>
        <div class="answers-container"></div>
        <div class="question-info" id="question_info-${index}"></div>
        <div class="question-category" id="question_category-${index}"></div>
        <div class="question-footer d-flex justify-content-between">
            <div class="question-points" id="points-${index}"></div>
            <div id="mandatory-${index}"></div>
            <div id="question_time-${index}"></div>
        </div>`
        container.setAttribute('class', 'container displayed-question')
        container.setAttribute('id', `question-${index}`)
        questionIndex++
        document.getElementById('questions').appendChild(container)
        $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" })
    }
    else{
        var container = document.getElementById('question-'+index)
        let file_div = container.querySelector('.displayed-title .attached-file')
        if(file_div){
            file_div.parentElement.removeChild(file_div)
        }
        container.querySelector('.answers-container').innerHTML=''
    }
    container.setAttribute('type', question.type)
    container.setAttribute('mandatory', question.mandatory)
    container.setAttribute('points', question.points)
    container.setAttribute('question-time', question.time)
    container.setAttribute('question-info', question.info)
    container.setAttribute('question-category', question.category)
    container.setAttribute('case-sensitive', question.case_sensitive)
    container.setAttribute('character-limit', question.character_limit)
    container.setAttribute('print-lines', question.print_lines)
    container.querySelector('.displayed-title h3').innerHTML = question.title;
    if(question.hasOwnProperty('file')){
        let div_tag = document.createElement('div')
        div_tag.setAttribute('class', 'attached-file')
        if(question.file.type=="image"){
            let img = document.createElement('img')
            img.src = question.file.src
            img.name = question.file.name
            img.setAttribute('class', 'image-lg')
            div_tag.appendChild(img)
        }
        else{
            let a = document.createElement('a')
            a.href = question.file.src
            a.name = question.file.name
            a.setAttribute('download' ,'')
            a.innerText = question.file.name
            div_tag.appendChild(a)
        }

        container.querySelector('.displayed-title').appendChild(div_tag)
    }

    let answers_container = container.querySelector('.answers-container')
    if(question.type=='file'){
        let input = document.createElement('input')
        input.setAttribute('type', question.type)
        input.setAttribute('disabled', '')
        answers_container.appendChild(input)
    }
    else if(question.type=='text'){
        let input = document.createElement('input')
        input.setAttribute('type', question.type)
        input.setAttribute('disabled', '')
        input.setAttribute('placeholder', 'Отговор, въведен от попълващия')
        answers_container.appendChild(input)
        let wrapper = document.createElement('div')
        wrapper.textContent = 'Правилни отговори:'
        for(let i=0; i<question.answers.length; i++){
            answer_el = document.createElement('div')
            answer_el.classList.add('correct-answer')
            answer_el.innerHTML = `<i class="answer-text">${question.answers[i].text}</i> - <span class="answer-points">${question.answers[i].points}</span> точки`
            wrapper.appendChild(answer_el);
        }
        answers_container.appendChild(wrapper)
    }
    else if(question.type=='radio' || question.type=='checkbox'){
        for(let i=0; i<question.answers.length; i++){
            let wrapper = document.createElement('div')
            wrapper.setAttribute('class', 'form-check')
            wrapper.setAttribute('id', `question-${index}-answer-${i}`)

            let input = document.createElement('input')
            input.setAttribute('class', 'form-check-input')
            input.setAttribute('type', question.type)
            input.setAttribute('id', `question-${index}-displayedInput-${i}`)
            input.setAttribute('value', question.answers[i].correct)
            input.setAttribute('disabled', '')

            let label = document.createElement('label')
            label.setAttribute('class', 'form-check-label')
            label.setAttribute('for', `question-${index}-displayedInput-${i}`)
            let answer_text = document.createElement('div')
            answer_text.setAttribute('class', 'answer-text')
            answer_text.textContent = question.answers[i].text
            label.appendChild(answer_text)

            if(question.answers[i].hasOwnProperty('file')){
                let div_tag = document.createElement('div')
                div_tag.setAttribute('class', 'attached-file')
                if(question.answers[i].file.type=="image"){
                    let img = document.createElement('img')
                    img.src = question.answers[i].file.src
                    img.name = question.answers[i].file.name
                    img.setAttribute('class', 'image-lg')
                    div_tag.appendChild(img)
                }
                else{
                    let a = document.createElement('a')
                    a.href = question.answers[i].file.src
                    a.name = question.answers[i].file.name
                    a.setAttribute('download' ,'')
                    a.innerText = question.answers[i].file.name
                    div_tag.appendChild(a)
                }
                label.appendChild(div_tag)
            }

            wrapper.appendChild(input)
            wrapper.appendChild(label)
            answers_container.appendChild(wrapper)
        }
    }
    if(question.info && question.info!='undefined'){
        container.querySelector('#question_info-'+index).textContent = 'Обяснение: '+question.info
    }
    else if(!question.info && container.querySelector('#question_info-'+index).textContent!=''){
        container.querySelector('#question_info-'+index).textContent=''
    }

    if(question.category && question.category!='undefined'){
        container.querySelector('#question_category-'+index).textContent = 'Категория: '+question.category
    }
    else if(!question.category && container.querySelector('#question_category-'+index).textContent!=''){
        container.querySelector('#question_category-'+index).textContent=''
    }
    container.querySelector('#points-'+index).textContent = question.points + ' точки'
    container.querySelector('#mandatory-'+index).textContent = question.mandatory ? 'Задължителен' : 'Незадължителен'
    container.querySelector('#question_time-'+index).textContent = question.time + ' секунди'

    questionIndex++
    $('#QuestionEditor').modal('hide')
    UpdateInfo()
}

//Return question object from modal content
function ConvertContent(){
    let question_title = document.getElementById('question-title').value.replace(/<(?!\/?\w>)/g, '&lt;').replace(/\n/g, '<br />');
    let mandatory = document.getElementById('mandatory').checked
    let points = document.getElementById('points').value
    let question_time = document.getElementById('question_time').value
    let question_info = document.getElementById('question_info').value
    let question_category = document.getElementById('question_category').value

    let answers = []
    if(selectedType=='radio' || selectedType=='checkbox'){
        for(let i=0; i<document.getElementById("answers").childElementCount; i++){
            let element = document.getElementsByClassName('answer')[i]
            let answer = {
                text: element.querySelector('input.form-control').value,
                correct: element.querySelector('.input-group-append .btn-group-toggle .switch input').checked
            }
            //File Check
            if(element.parentElement.querySelector('.collapse .card img').getAttribute('type')){
                let file = {
                    name: element.parentElement.querySelector('.collapse .card img').name,
                    src: element.parentElement.querySelector('.collapse .card img').src,
                    type: element.parentElement.querySelector('.collapse .card img').getAttribute('type')
                }
                answer.file = file
            }
    
            answers.push(answer)
        }
    }
    else if(selectedType=='text'){
        for(let i=0; i<document.getElementsByClassName('text-answer').length; i++){
            let el = document.getElementsByClassName('text-answer')[i];
            let text = el.querySelector('.form-control').value;
            let points = parseInt(el.querySelector('.points-control').value);
            if(text!=''){
                answers.push({text, points})
            }
            
        }
    }

    let question_obj = {
        title: question_title,
        type: selectedType,
        points,
        mandatory,
        time: question_time,
        info: question_info,
        category: question_category,
        answers
    }

    //Question File Check
    if(document.getElementById('file-0').getAttribute('type')){
        let file = {
            name: document.getElementById('file-0').name,
            src: document.getElementById('file-0').src,
            type: document.getElementById('file-0').getAttribute('type')
        }
        question_obj.file = file
    }

    if(selectedType=='text'){
        question_obj.case_sensitive = document.getElementById('case-sensitive').checked;
        question_obj.character_limit = document.getElementById('character-limit').value;
        question_obj.print_lines = document.getElementById('print-lines').value;
    }

    return question_obj
}

//Recieve question id
//Return question object 
function QuestionObject(question_index){
    let question = document.getElementById('question-'+question_index)
    let question_title = document.getElementById('question-title-'+question_index).innerHTML
    let type = question.getAttribute('type')
    let mandatory = (question.getAttribute('mandatory') == 'true')
    let points = question.getAttribute('points')
    let question_time = question.getAttribute('question-time')
    let question_info = question.getAttribute('question-info')
    let question_category = question.getAttribute('question-category')
    let answers = []
    if(type=='radio' || type=='checkbox'){
        for(let i=0; i<question.querySelector(`.answers-container`).childElementCount; i++){
            let answer = {              
                text: question.querySelector(`#question-${question_index}-answer-${i} label .answer-text`).innerHTML.replace(/&lt;/g, '<').replace(/&gt;/g, '>'),
                correct: (question.querySelector(`#question-${question_index}-answer-${i} input`).value == 'true')
            }
    
            //File Check
            if(question.querySelector(`#question-${question_index}-answer-${i} label div a`)){
                let file = {
                    name: question.querySelector(`#question-${question_index}-answer-${i} label div a`).name,
                    src: question.querySelector(`#question-${question_index}-answer-${i} label div a`).href,
                    type: 'application'
                }
                answer.file = file
            }
            else if(question.querySelector(`#question-${question_index}-answer-${i} label div img`)){
                let file = {
                    name: question.querySelector(`#question-${question_index}-answer-${i} label div img`).name,
                    src: question.querySelector(`#question-${question_index}-answer-${i} label div img`).src,
                    type: 'image'
                }
                answer.file = file
            }
    
            answers.push(answer)
        }
    }
    else if(type=='text'){
        for(let i=0; i<question.querySelectorAll('.correct-answer').length; i++){
            let correct_answer = question.querySelectorAll('.correct-answer')[i]
            let answer = {
                text: correct_answer.querySelector('.answer-text').textContent,
                points: parseInt(correct_answer.querySelector('.answer-points').textContent)
            }
            answers.push(answer)
        }
    }

    let question_obj = {
        title: question_title,
        type,
        points,
        mandatory,
        time: question_time,
        info: question_info,
        category: question_category,
        answers
    }

    //Question File Check
    if(question.querySelector(`.displayed-title div a`)){
        let file = {
            name: question.querySelector(`.displayed-title div a`).name,
            src: question.querySelector(`.displayed-title div a`).href,
            type: 'application'
        }
        question_obj.file = file
    }
    else if(question.querySelector(`.displayed-title div img`)){
        let file = {
            name: question.querySelector(`.displayed-title div img`).name,
            src: question.querySelector(`.displayed-title div img`).src,
            type: 'image'
        }
        question_obj.file = file
    }

    if(type=='text'){
        question_obj.case_sensitive = question.getAttribute('case-sensitive')
        question_obj.character_limit = question.getAttribute('character-limit')
        question_obj.print_lines = question.getAttribute('print-lines')
    }
    return question_obj
}

//Update question count, total points counter and categories
function UpdateInfo(){
    document.getElementById('question-count').textContent = document.getElementsByClassName('displayed-question').length;
    let points_array = Array.from(document.querySelectorAll('.question-points')).map(el=>el.textContent.split(' ')[0])
    if(points_array){
        document.getElementById('total-points').textContent = points_array.reduce((a,b) => parseInt(a)+parseInt(b))
    }
    else{
        document.getElementById('total-points').textContent = '0'
    }
    let categories = Array.from(document.getElementsByClassName('question-category')).map(cat=>cat.textContent.slice(cat.textContent.indexOf(':')+1)).filter(cat=>cat!='')
    categories = [...new Set(categories)];
    let existing_categories = Array.from(document.getElementsByClassName('question-category-list')).slice(1).map(cat=>cat.textContent);
    categories.forEach(function(category){
        if(existing_categories.includes(category)==false){
            document.getElementById('question-category-container').innerHTML+=
            `<span class="question-category-list active" id="category-${category}" onclick="ToggleCategory(this)">${category}</span>`
        }
    })
    existing_categories.forEach(function(category){
        if(categories.includes(category)==false){
            document.getElementById('category-'+category).parentElement.removeChild(document.getElementById('category-'+category));
        }
    })
}
function ToggleCategory(el){
    if(el.id=='default-category'){
        Array.from(document.getElementsByClassName('question-category-list')).slice(1).forEach(function(el){el.classList.remove('inactive');el.classList.add('active')});
        Array.from(document.querySelectorAll('.displayed-question.hidden')).forEach(function(el){
            el.classList.remove('hidden');
            setTimeout(function () {
                el.classList.remove('visuallyhidden');
            }, 20);
        })
    }
    else{
        if(el.classList.contains('active')){
            el.classList.remove('active')
            el.classList.add('inactive')
            Array.from(document.querySelectorAll(`[question-category="${el.textContent.trim()}"]`)).forEach(function(el){
                el.classList.add('visuallyhidden');    
                el.addEventListener('transitionend', function(e) {
                    el.classList.add('hidden'); 
                }, {
                capture: false,
                once: true,
                passive: false
                });
            })
        }
        else{
            el.classList.remove('inactive')
            el.classList.add('active')
            Array.from(document.querySelectorAll(`[question-category="${el.textContent.trim()}"]`)).forEach(function(el){
                el.classList.remove('hidden');
                setTimeout(function () {
                    el.classList.remove('visuallyhidden');
                }, 20);
            })
        }
    }
}

//Move question up
function MoveUp(index){
    let element = document.getElementById('question-'+index);
    if(element.previousElementSibling)
    element.parentNode.insertBefore(element, element.previousElementSibling);
    $('[data-toggle="tooltip"]').tooltip("hide");
}
//Move question down
function MoveDown(index){
    let element = document.getElementById('question-'+index);
    if(element.nextElementSibling)
    element.parentNode.insertBefore(element.nextElementSibling, element);
    $('[data-toggle="tooltip"]').tooltip("hide");
}
//Remove question
function RemoveQuestion(index) {
    $('[data-toggle="tooltip"]').tooltip("hide");
    if(confirm('Сигурни ли  сте, че искате да изтриете този въпрос?')){
        let elem = document.getElementById('question-' + index);
        elem.parentNode.removeChild(elem);
        UpdateInfo()
    }
}

//Duplicate a question
function DuplicateQuestion(index) {
    ImportQuestion(QuestionObject(index))
}

//Clear all test content
function Clear() {
    if (confirm('Да се изчисти ли цялто съдържание на теста?')) {
        document.getElementById('title').value = '';
        document.getElementById('questions').innerHTML = '';
        questionIndex = 0;
        UpdateInfo()
    }
}

//Open modal for question importing
function OpenQuestionImport(){
    document.getElementById('test-choose').style.display = 'block'
    document.getElementById('question-choose').style.display = 'none'
    LoadTestList()
    $('#QuestionImport').modal('show')
}
function LoadTestList(){
    $.ajax({
        type: 'POST',
        url: '/actions/info/profile_created_tests.php',
        dataType: 'json',
        success: function(tests){
            let container = document.getElementById('test-list');
            container.innerHTML = '';
            tests.forEach(function(test){container.innerHTML+=
                `<label class="btn btn-light btn-lg" style="width:100%; margin:4px;cursor:pointer">
                <input type="radio" name="test-choice" value="${test.id}">${test.test_name}
                </label>`})
            document.getElementById('btn-import').onclick = function(){LoadQuestionList()}
        }
    })
}
function LoadQuestionList(){
    $.ajax({
        type: 'POST',
        url: '/actions/info/get_test.php',
        dataType: 'json',
        data:{
            testId: $("input[name=test-choice]:checked").val()
        },
        success: function(test){
            import_test = test;
            let container = document.getElementById('question-list');
            container.innerHTML = '';
            test.questions.forEach(function(question){container.innerHTML+=
            `<label class="btn btn-light btn-lg" style="width:100%; margin:4px;cursor:pointer">
            <input type="checkbox" name="question-choice" value="${question.id}">${question.title}
            </label>`})
            document.getElementById('btn-import').onclick = function(){ImportSelected()}

        },
        beforeSend: function(){
            document.getElementById('test-choose').style.display = 'none'
            document.getElementById('question-choose').style.display = 'block'
        }
    })
}
function ImportSelected(){
    let selected_id = $("input[name=question-choice]:checked").get().map(el=>el.value)
    console.log(selected_id)
    selected_id.forEach(id=>ImportQuestion(import_test.questions.filter(question=>question.id==id)[0]))
    $('#QuestionImport').modal('hide')
}
//Save test and settings
function Save() {
    let content = [];
    Array.from(document.getElementsByClassName('displayed-question')).forEach(element=>content.push(QuestionObject(getElementIndex(element))))
     $.ajax({  
        type: 'POST',  
        url: '/actions/crud/save_test.php', 
        data: {
        testId: new URLSearchParams(window.location.search).get('testId'),
        name: document.getElementById('title').value,
        test_content: content,
        unlocked: document.getElementById('unlocked').checked ? 1 : 0,
        public: document.getElementById('public').checked ? 1 : 0,
        grading: document.getElementById('grading').value,
        one_per_one: document.getElementById('one_per_one').checked ? 1 : 0,
        randomize_questions: document.getElementById('randomize_questions').checked ? 1 : 0,
        randomize_answers: document.getElementById('randomize_answers').checked ? 1 : 0,
        question_limit: document.getElementById('question_limit').value,
        time_limit: document.getElementById('time_limit').value,
        individual_time: document.getElementById('individual_time').checked ? 1 : 0,
        require_profile: document.getElementById('require_profile').checked ? 1 : 0,
        allow_anonymous: document.getElementById('allow_anonymous').checked ? 1 : 0,
        limit_response: document.getElementById('limit_response').checked ? 1 : 0,
        check_points: document.getElementById('check_points').checked ? 1 : 0,
        check_answers: document.getElementById('check_answers').checked ? 1 : 0,
        limit_check: document.getElementById('limit_check').checked ? 1 : 0,
        team_limit: document.getElementById('team_limit').value,
        tags: Array.from(document.getElementsByClassName('tag')).map(tag => tag.firstChild.textContent).join(', ')
        },
        success: function(testId){
            let regex = /index:(\d+)/gm;
            let id = regex.exec(testId)[1]
            console.log(testId)
            window.history.replaceState(null, null, `?testId=${id}`);
            document.getElementsByClassName('toast-body')[0].innerHTML='Промените бяха успешно запазени!'
            $('.toast').toast('show');
        },
        error: function(msg){
            document.getElementsByClassName('toast-body')[0].innerHTML=':( Възника проблем<br>Моля опитайте по-късно';
            console.log(msg)
            $('.toast').toast('show');
        }
    });
}