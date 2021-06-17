function SendInfo(type, redirect=false) {
  if(type=='login'){
      $.ajax({
          type: 'POST',
          url: '/actions/user/login.php',
          data: {
              name: document.getElementById('log_username').value,
              password: document.getElementById('log_password').value
          },
          success: function(errors) {
              if(!errors){
                if(redirect){
                  if(urlParams.get('path')=='profile'){
                      window.location.href= 'profile.php'
                  }
                  else if(urlParams.get('path')=='quiz'){
                      window.location.href = `quiz.php?testId=${urlParams.get('testId')}&name=${urlParams.get('name')}&class=${urlParams.get('class')}&division=${urlParams.get('division')}`
                  }
                  else{
                      window.location.href = 'index.php'
                  }
                }
                window.location.href='profile.php'
              }
              else{
                  document.getElementById('log_errorBox').innerHTML=`<p>${errors}</p>`
              }
          },
          error: function(err){
              document.getElementById('log_errorBox').innerHTML=`<p>${err}</p>`
          }
      });
  }
  else if(type=='register'){
      $.ajax({
          type: 'POST',
          url: '/actions/user/register.php',
          data: {
              name: document.getElementById('reg_username').value,
              email: document.getElementById('reg_email').value,
              password_1:  document.getElementById('password_1').value,
              password_2:  document.getElementById('password_2').value,
              firstName: document.getElementById('firstName').value,
              surname: document.getElementById('Surname').value,
              role: $('input[name=role]:checked', '#selected-role').val(),
              class: document.getElementById('reg_class').value,
              division: document.getElementById('reg_division').value
          },
          success: function(errors) {
              console.log(errors)
              if(!errors){
                if(redirect){
                  if(urlParams.get('path')=='profile'){
                      window.location.href= 'profile.php'
                  }
                  else if(urlParams.get('path')=='quiz'){
                      window.location.href = `quiz.php?testId=${urlParams.get('testId')}&name=${urlParams.get('name')}&class=${urlParams.get('class')}&division=${urlParams.get('division')}`
                  }
                  else{
                      window.location.href = 'index.php'
                  }
                }
                window.location.href='profile.php'
              }
              else{
                  document.getElementById('reg_errorBox').innerHTML=errors;
              }
              
          },
      });
  }
 
}
window.onload = function(){
    $("#teacher-button").click(function(){
        $("#optional").slideUp('fast');
    });
    $("#stu-button").click(function(){
    $("#optional").slideDown('fast');
    });
}