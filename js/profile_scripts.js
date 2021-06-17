google.load("visualization", "1", {packages:["corechart"]});
document.querySelector("#nav-search").addEventListener("keydown", function(event) {
    if(event.key === 'Enter') {
        event.preventDefault();
        displaySearchView(document.getElementById('nav-search').value)   
    }
}, false);
window.onload = function(){
    let screen = new URLSearchParams(window.location.search).get('v')
    if(screen=='overview' || screen==null){
      displayOverview()
      document.getElementById('overview-link').classList.add('active')
    }
    else if(screen=='created-tests'){
      displayCreatedTests('teacher')
      document.getElementById('created-tests-link').classList.add('active')
    }
    else if(screen=='responses'){
        displayResponsesView()
        document.getElementById('responses-link').classList.add('active')
    }
    else if(screen=='search'){
        displaySearchView(new URLSearchParams(window.location.search).get('search'))
        document.getElementById('search-link').classList.add('active')
    }
    else if(screen=='create-team'){
        displayTeamCreatorView();
    }
    else if(screen=='view-team'){
        OpenTeamView(new URLSearchParams(window.location.search).get('team'))
    }
    $.fn.dataTable.moment( 'HH:mm DD/MM/YY');
    moment.locale('bg');
    $('#test-table').DataTable({
        language: {
            searchPlaceholder: "Търсене...",
            search: "",
        },
        oLanguage: {
            "sZeroRecords": "Няма намерени тестове",
            "sEmptyTable": '<a href="/create.php" class="btn btn-block btn-light">Създайте своя първи тест!</a>'
        },
        autoWidth: true,
        paging: false,
        info: false,
        order: [[ 7, "desc" ]],
        columns: [
            {"orderable": false},
            null,
            {"orderable": false},
            {"orderable": false},
            null,
            null,
            {"orderable": false},
            null
        ]
    }); 
    $('#responseTable').DataTable({
        language: {
            searchPlaceholder: "Търсене",
            search: "",
        },
        "oLanguage": {
            "sZeroRecords": "Няма намерени резултати",
            "sEmptyTable": 'Няма изпратени резултати'
        },
        "paging": false,
        "info": false,
        "order": [[ 5, "asc" ]],
        "columns": [
            null,
            null,
            null,
            null,
            {"orderable": false},
            null,
            null,
            {"orderable": false}
        ],
        "columnDefs": [
            {
            "targets": [ 3 ],
            "visible": false
            },
            { "orderData": [ 3 ],    "targets": 2 }
        ]
    });
    $('[data-toggle="tooltip"]').tooltip({
      trigger : 'hover'
    })
    readyTags()
    ReloadTeams()
  };
function displayTab(el){
    if(document.querySelector('a.active')){
        document.querySelector('a.active').classList.remove('active');
    }
    el.classList.add('active')
    Array.from(document.querySelectorAll('.screen')).forEach(el=>el.style.display='none')
    
    if(el.id=='overview-link'){
        displayOverview()
    }
    else if(el.id=='created-tests-link'){
        displayCreatedTests('teacher')
    }
    else if(el.id=='responses-link'){
        displayResponsesView()
    }
    else if(el.id=='search-link'){
        displaySearchView()
    }
    else if(el.id=="create-team-link"){
        displayTeamCreatorView()
    }
    else if(el.id=="view-team-link"){
        let team_id = el.getAttribute('team');
        OpenTeamView(team_id)
    }
}
function displayTeamCreatorView(){
    document.querySelector('.create-team').style.display = 'block';
    window.history.replaceState(null, null, `?v=create-team`);
    ReloadTeams()
}
function displayOverview(){
    $.ajax({
        type:"POST",
        url:"/actions/info/teacher_overview_tests.php",
        dataType: 'json',
        success: function(obj){
            document.getElementById('tests-count').textContent = obj.created_tests_count;
            let node = document.querySelector('#active-tests-table tbody');
            while (node.lastElementChild) {
                node.removeChild(node.lastElementChild);
            }
            obj.active_tests.forEach(function(test){
                let container = document.createElement('tr');
                container.innerHTML += `<td>${test.test_name}</td>`;
                container.innerHTML += `<td>${moment(test.sent_on).fromNow()}</td>`;
                container.innerHTML += `<td><button type="button" class="btn btn-warning btn-sm" id="test-${test.id}" onclick="GetResponses(this.id)">
                <img src="images/view_list.svg" width="18" height="18"> Резултати
                </button></td>`;
                node.appendChild(container)
            })
        },
        error: function(e){
            console.log(e.responseText)
        }
    })
    $.ajax({
        type:"POST",
        url:"/actions/info/teacher_overview_responses.php",
        dataType: 'json',
        success: function(obj){
            document.getElementById('total-responses').textContent = obj.total;
            document.getElementById('undecided-count').textContent = obj.responses.length;
            document.getElementById('active-count').textContent = obj.active;
            let node = document.querySelector('#undecided-responses tbody');
            while (node.lastElementChild) {
                node.removeChild(node.lastElementChild);
            }
            obj.responses.forEach(function(response){
                let container = document.createElement('tr');
                container.innerHTML += `<td>${response.fullName}</td>`;
                container.innerHTML += `<td>${response.class}</td>`;
                container.innerHTML += `<td>${response.test_name}</td>`;
                container.innerHTML += `<td>
                <a class="btn btn-sm btn-light" href="/viewscore.php?viewscore=${response.pin}"><img src="/images/visit.svg"/> Прегледай</a></td>`;
                node.appendChild(container)
            })
        },
        error: function(e){
            console.log(e)
        }
    })
    document.querySelector('.overview').style.display = 'flex';
    google.setOnLoadCallback(drawDonutChart);
    function drawDonutChart() {
        var data = google.visualization.arrayToDataTable([
        ['Оценка', 'Брой'],
        ['Отличен', 4],
        ['Мн. Добър', 5],
        ['Добър',  2],
        ['Среден', 1],
        ['Слаб', 0]
        ]);
        var options = {
            title: 'Оценки',
            chartArea: {
                width: '100%'
            },
            height: 450
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
    window.history.replaceState(null, null, `?v=overview`);
}
function displayCreatedTests(){
    $.ajax({
        type:"POST",
        url:"/actions/info/profile_created_tests.php",
        dataType: 'json',
        success: function(tests){
            $('[data-toggle="tooltip"]').tooltip('hide');
            let table = $('#test-table').DataTable();
            table.clear().draw().columns.adjust()
            tests.forEach(function(test){
                table.row.add([
                    test.pin,
                    test.test_name,
                    test.tags,
                    `<label class="switch">
                    <input type="checkbox" 
                    id="visible-${test.id}"
                    ${test.unlocked=='1'?'checked':''} 
                    onclick="SetUnlocked(this)">
                    <span></span>
                    </label>`,
                    test.questions,
                    test.points,
                    `<a href="/create.php?testId=${test.id}" class="btn btn-sm edit test-btn" data-toggle="tooltip"
                    data-placement="bottom" title="Редактирай">
                    <img src="images/edit.svg" width="18" height="18"></a>
                    <button onclick="DeleteTest(${test.id})" class="btn btn-sm btn-danger test-btn" data-toggle="tooltip"
                        data-placement="bottom" title="Изтрий">
                        <img src="images/delete.svg" width="18" height="18"></button>
                    <button onclick="DuplicateTest(${test.id})" type="submit" class="btn btn-sm duplicate test-btn" data-toggle="tooltip"
                        data-placement="bottom" title="Създай Копие">
                        <img src="images/file_copy.svg" width="18" height="18"></button>
                    <button type="button" class="btn btn-warning btn-sm test-btn" onclick="GetResponses(this.id)" id="response-${test.id}"
                        data-toggle="tooltip" data-placement="bottom" title="Резултати">
                        <img src="images/view_list.svg" width="18" height="18">
                    </button>
                    <a href="/stats.php?id=${test.id}" class="btn btn-sm stats test-btn" data-toggle="tooltip"
                        data-placement="bottom" title="Статистика">
                        <img src="images/graph.png" width="18" height="18">
                    </a>
                    <button type="button" class="btn btn-sm btn-light test-btn" onclick="GetSettings(this.id)" id="settings-${test.id}"
                        data-toggle="tooltip" data-placement="bottom" title="Настройки">
                        <img src="images/settings.svg">
                    </button>
                    <a href="/print_pdf.php?id=${test.id}" target="_blank" class="btn btn-info btn-sm test-btn" data-toggle="tooltip"
                        data-placement="bottom" title="Печат">
                        <img src="/images/print-white.svg" width="18" height="18"/>
                    </a>`,
                    test.last_modified
                ]).draw()
            })
            table.columns.adjust()
            $('[data-toggle="tooltip"]').tooltip({
                trigger : 'hover'
            })
        },
        error: function(e){
            console.log(e.responseText)
        }
    })
    document.querySelector('.created-tests').style.display = 'block';
    window.history.replaceState(null, null, `?v=created-tests`);
}
function displayResponsesView(){
    $.ajax({
        type:"POST",
        url:"/actions/info/profile_responses.php",
        dataType: 'json',
        success: function(responses){
            mean = responses.reduce((a,b) => ({grade:a.grade+b.grade})).grade/responses.length;
            let median;
            if(responses.length%2==0){
                median = (responses[(responses.length/2)-1].grade+responses[responses.length/2].grade)/2
            }
            else{
                median = responses[Math.floor(responses.length/2)-1]
            }
            let grade_map = {}
            for(let i=0; i<responses.length; i++){
                let num = responses[i].grade, grade;
                if(num<3) grade=2;
                else if(num<3.5) grade=3;
                else if(num<4.5) grade=4;
                else if(num<5.5) grade=5;
                else if(num<=6) grade=6;

                if(grade_map[grade]!=null){
                    grade_map[grade]++
                }
                else{
                    grade_map[grade]=0
                }
            }
            let keys = Object.keys(grade_map)
            let largest = Math.max.apply(null, keys.map(x => grade_map[x]))
            let mode = keys.reduce((result, key) => { if (grade_map[key] === largest){ result.push(key); } return result; }, []);
            document.getElementById('median').textContent = TextifyGrade(median.grade);
            document.getElementById('mean').textContent = TextifyGrade(mean);
            document.getElementById('mode').textContent = mode.map(a=>TextifyGrade(a)).join(',');
            let chart_data = responses.map(resp=>(['', parseFloat(resp.grade.toFixed(2))]))
            google.setOnLoadCallback(drawCurvedChart);
            function drawCurvedChart() {
                var data = google.visualization.arrayToDataTable([['','Оценка']].concat(chart_data));
                var options = {
                    title: 'Успех',
                    chartArea: {
                        width: '97%'
                    },
                    vAxis:{textPosition: 'in'},
                    width: '90%',
                    legend: { position: 'left' }
                };
                var chart = new google.visualization.LineChart(document.getElementById('curved_chart_stu'));
                chart.draw(data, options);
            }

            let node = document.querySelector('#profile-responses-table tbody');
            while (node.lastElementChild) {
                node.removeChild(node.lastElementChild);
            }
            responses = responses.reverse()
            responses.forEach(function(response){
                let container = document.createElement('tr');
                container.innerHTML += `<td>${response.test_pin}</td>`;
                container.innerHTML += `<td>${response.test_name}</td>`;
                container.innerHTML += `<td>${response.grade_text}(${response.grade})</td>`;
                container.innerHTML += `<td>
                <span style="color:green">${response.points}</span>
                <span style="color:orange">${response.undecided==0?"":'~ '+response.undecided}</span> /
                ${response.maxpoints}</td>`;
                container.innerHTML += `<td>${response.sent_on}</td>`;
                container.innerHTML += `<td>
                <a class="btn btn-sm btn-light" href="/viewscore.php?viewscore=${response.pin}"><img src="/images/visit.svg"/> Прегледай</a></td>`;
                node.appendChild(container)
            })
            
        },
        error: function(e){
            console.log(e.responseText)
        }
    })
    document.querySelector('.responses').style.display = 'block';
    window.history.replaceState(null, null, `?v=responses`);
}
function displaySearchView(search_string){
    Array.from(document.querySelectorAll('.screen')).forEach(el=>el.style.display='none');
    if(document.querySelector('a.active')){
        document.querySelector('a.active').classList.remove('active');
    }
    document.getElementById('search-link').classList.add('active')
    if(search_string){
        document.getElementById('search-field').value = search_string;
        document.getElementById('nav-search').value = '';
        $.ajax({
            type: "POST",
            url: "/actions/info/search_tests.php",
            dataType: 'json',
            data:{
                search_string
            },
            success: function(tests){
                let container = document.getElementById('search-results')
                container.innerHTML = '';
                tests.forEach(function(test){
                    let wrapper = document.createElement('div');
                    wrapper.innerHTML= `<div class="search-result">  
                    <div style="font-size:23px;">${test.test_name}</div>
                    <div style="font-size:13px; color:gray">PIN: ${test.pin}</div>
                    <div style="font-size:13px; color:gray; margin-top:-3px;">Автор: ${test.fullName}</div>
                    <div>
                    <button onclick="DuplicateTest(${test.id})" type="submit" class="btn btn-sm duplicate">
                        <img src="images/file_copy.svg" width="18" height="18"> Създай Копие
                    </button>
                    <a href="/quiz.php?testId=${test.pin}&name&class&division" class="btn btn-sm btn-warning">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                    </svg>
                        Отвори за попълване
                    </a>
                    </div>
                    </div>`;
                    container.appendChild(wrapper)
                })
            }
        })
    }
    document.querySelector('.search').style.display = 'block';
    if(search_string){
        window.history.replaceState(null, null, `?v=search&search=`+search_string);
    }
    else{
        window.history.replaceState(null, null, `?v=search`);
    }
}
function ReloadTeams(){
    $.ajax({
        type: "POST",
        url: "/actions/info/get_teams.php",
        dataType: "json",
        success: function(teams){
            let container = document.getElementById('teams-list')
            container.innerHTML='';
            teams.forEach(team=>container.innerHTML+=`
            <li class="nav-item">
            <a class="nav-link" onclick="displayTab(this)" id="view-team-link" team="${team.id}" href="#">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-people" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1h7.956a.274.274 0 0 0 .014-.002l.008-.002c-.002-.264-.167-1.03-.76-1.72C13.688 10.629 12.718 10 11 10c-1.717 0-2.687.63-3.24 1.276-.593.69-.759 1.457-.76 1.72a1.05 1.05 0 0 0 .022.004zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10c-1.668.02-2.615.64-3.16 1.276C1.163 11.97 1 12.739 1 13h3c0-1.045.323-2.086.92-3zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
            </svg>
            ${team.name}
            </a>
            </li>`)
        }
    })
}
function CreateTeam(){
    let name = document.getElementById('create-team-field').value;
    if(name){
        $.ajax({
            type:"POST",
            url:"/actions/crud/create_team.php",
            data:{
                name
            },
            success: function(id){
                ReloadTeams();
                OpenTeamView(id);
            }
        })
    }
    else{
        alert('Моля въведете име')
    }
}
function OpenTeamView(id){
    $.ajax({
        type:"POST",
        url:"/actions/info/get_team_info.php",
        dataType:'json',
        data:{
            id
        },
        success:function(team){
            document.getElementById('team-name').textContent = team.team_name;
            document.getElementById('team-creator').textContent = team.creator_name;
            let container = document.getElementById('team-members')
            container.innerHTML = ''
            team.members.forEach(member=> container.innerHTML+=member+'<br>')
        }
    })
    document.querySelector('.create-team').style.display = 'none';
    document.querySelector('.view-team').style.display = 'block';
    window.history.replaceState(null, null, `?v=view-team&team=${id}`);
}
function AddMember(){
    let member = document.getElementById("add-member-field").value;
    let curent_team = new URLSearchParams(window.location.search).get('team');
    $.ajax({
        type:"POST",
        url:"/actions/crud/add_member.php",
        data:{
            member,
            team_id: curent_team
        },
        success: function(){
            OpenTeamView(curent_team)
            document.getElementById("add-member-field").value = ''
        },
        error: function(msg){
            console.log(msg)
        }
    })
}
function GetResponses(element_id){
  $('#responseModal').modal('show')
  let test_id = element_id.substr(element_id.indexOf('-') + 1);
  document.getElementsByClassName('reload')[0].id = 'reload-'+test_id
  $('#responseTable').DataTable().clear();
  $.ajax({
      type: 'POST',
      url: '/actions/info/get_responses.php',
      data: {
          test_id
      },
      dataType: 'json',
      success: function(responses){
          let table = $('#responseTable').DataTable();
          $('[data-toggle="tooltip"]').tooltip('hide')
          responses.forEach(response=>{
              table.row.add( [
                  response.name,
                  `${response.class}${response.division}`,
                  `${response.grade_text}(${response.grade})`,
                  response.grade,
                  `<span style="color:green">${response.points}</span>/${response.maxpoints}
                  ${response.undecided?`- <span style="color:orange">${response.undecided} недооценени</span>`:''}`,
                  response.state=='active'?'-/-':response.date,
                  response.state=='active'?
                  `Активен <button type="button" class="btn btn-sm btn-light"
                  data-toggle="tooltip" data-placement="bottom" title="Прекрати тест" onclick="HaltResponse(${response.pin})">
                  <img src="/images/hand-black.svg"/></button>`
                  :"Изпратен",
                  `<a class="btn btn-sm btn-light" href="viewscore.php?viewscore=${response.pin}" target="_blank"
                  data-toggle="tooltip" data-placement="bottom" title="Прегледай"><img src="/images/visit.svg"/></a>
                  <button type="button" class="btn btn-sm btn-danger" onclick="DeleteResponse(${response.id})"
                  data-toggle="tooltip" data-placement="bottom" title="Изтрий"><img src="/images/delete.svg"/></button>`
              ]).draw()
          })
          $('[data-toggle="tooltip"]').tooltip()
          if(responses.length>0){
              document.getElementById('response-average').innerHTML = (responses.reduce((a,b)=>({grade: a.grade+b.grade})).grade/responses.length).toFixed(2)
          }
          else{
              document.getElementById('response-average').innerHTML = 'Липсва'
          }
      },
      error: function(log){
          console.log(log.responseText);
      }
  })
  $('#responseModal').modal('show')
}
function DeleteResponse(response_id){
    if(confirm('Сигурни ли сте, че искате да изтриете този резултат?')){
        $.ajax({
            type: "POST",
            url: "/actions/crud/delete_response.php",
            data:{
                response_id
            },
            success: function(){
                GetResponses(document.getElementsByClassName('reload')[0].id);
            }
        })
    }
    
}
function HaltResponse(response_pin){
    $.ajax({
        type: "POST",
        url: "/actions/crud/finish_response.php",
        data:{
            response_pin
        },
        success: function(){
            GetResponses(document.getElementsByClassName('reload')[0].id);
        }
    })
}
function DeleteTest(test_id){
    if(confirm('Сигурни ли сте, че искате да изтриете теста?')){
        $.ajax({
            type: "POST",
            url: "/actions/crud/delete_test.php",
            data: {
                test_id
            },
            success: function(){
                displayCreatedTests()
            },
            error: function(e){
                console.log(e.responseText)
            }
        })
    }
}
function DuplicateTest(test_id){
    $.ajax({
        type: "POST",
        url: "/actions/crud/duplicate_test.php",
        data: {
            test_id
        },
        success: function(){
            displayTab(document.getElementById('created-tests-link'))
        },
        error: function(e){
            console.log(e.responseText)
        }
    })
}
function GetSettings(element_id){
  let test_id = element_id.substr(element_id.indexOf('-') + 1);
  document.getElementsByClassName('refresh-settings')[0].id = 'refreshSettings-'+test_id
  document.getElementsByClassName('save-settings')[0].id = 'SetSettings-'+test_id
  clearTags()
  $.ajax({
      type:"POST",
      url: "/actions/info/get_settings.php",
      data:{
          test_id
      },
      dataType: "json",
      success: function(settings){
          document.getElementById('public').checked = parseInt(settings.public)
          document.getElementById('grading').value = settings.grading
          document.getElementById('one_per_one').checked = parseInt(settings.one_per_one)
          document.getElementById('randomize_questions').checked = parseInt(settings.randomize_questions)
          document.getElementById('randomize_answers').checked = parseInt(settings.randomize_answers)
          document.getElementById('question_limit').value = parseInt(settings.question_limit)
          document.getElementById('time_limit').value = parseInt(settings.time_limit)
          document.getElementById('individual_time').checked = parseInt(settings.individual_time)
          document.getElementById('require_profile').checked = parseInt(settings.require_profile)
          document.getElementById('allow_anonymous').checked = parseInt(settings.allow_anonymous)
          document.getElementById('limit_response').checked = parseInt(settings.limit_response)
          document.getElementById('check_points').checked = parseInt(settings.check_points)
          document.getElementById('check_answers').checked = parseInt(settings.check_answers)
          document.getElementById('limit_check').checked = parseInt(settings.limit_check)
          document.getElementById('team_limit').value = settings.team_limit
          addTags(settings.tags.split(','))
      },
      error: function(error){
          console.log(error.responseText)
      }
  })
  $('#settingsModal').modal('show')
}
function SetSettings(element_id) {
  let test_id = element_id.substr(element_id.indexOf('-') + 1);
  $.ajax({
      type: 'POST',
      url: '/actions/crud/update_settings.php',
      data: {
          test_id,
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
      success: function(){
        displayCreatedTests()
          $('#settingsModal').modal('hide')
      }
  });
  
}
function SetUnlocked(element){
  let test_id = element.id.substr(element.id.indexOf('-') + 1);
  $.ajax({
      type: 'POST',
      url: '/actions/crud/update_locked_setting.php',
      data: {
          test_id,
          state: element.checked?1:0
      },
  });
}
function TextifyGrade(num){
    let string;
    if(num<3) string='Слаб';
    else if(num<3.5) string='Среден';
    else if(num<4.5) string='Добър';
    else if(num<5.5) string='Мн.добър';
    else if(num<=6) string='Отличен';
    return string+'('+parseFloat(num).toFixed(2)+')'
}