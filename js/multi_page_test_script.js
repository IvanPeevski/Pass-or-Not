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
          if (document.getElementsByName('name_field')[0]) {
              if (document.getElementsByName('name_field')[0].value == '') {
                  document.getElementsByName('name_field')[0].value = 'Без име';
              }
          }
          document.getElementsByName('submit')[0].click()
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
  setInterval(timer, 1000);
}
function scrollToTarget(id){
  let element = document.getElementById(id);
  let offset = 45;
  let bodyRect = document.body.getBoundingClientRect().top;
  let elementRect = element.getBoundingClientRect().top;
  let elementPosition = elementRect - bodyRect;
  let offsetPosition = elementPosition - offset;

  window.scrollTo({
  top: offsetPosition,
  behavior: 'smooth'
  });
}
function SubmitRequest(){
  let checker = true;
  let unselected = []
  for(let i=0; i<document.getElementsByClassName('questionBody').length; i++){
      if(document.getElementsByClassName('questionBody')[i].classList.contains('required')){
          if(document.getElementsByClassName(`question-${i}-answers`)[0].type=='radio'){
            if(Array.from(document.getElementsByName(`question-${i}-answers`)).filter(el=> el.checked).length==0){
                console.log('here')
                checker=false;
                unselected.push(i)
            }
          }
          else if(document.getElementsByClassName(`question-${i}-answers`)[0].type == 'checkbox'){
            if(Array.from(document.getElementsByName(`question-${i}-answers[]`)).filter(el=> el.checked).length==0){
                checker=false;
                unselected.push(i)
            }
          }
          else if(document.getElementsByClassName(`question-${i}-answers`)[0].tagName=='TEXTAREA'){
              if(document.getElementsByName(`question-${i}-answers`)[0].value==''){
                  checker= false;
                  unselected.push(i)
              }
          }
          else if(document.getElementsByClassName(`question-${i}-answers`)[0].type=='file'){
              if(document.getElementsByName(`question-${i}-answers`)[0].value==''){
                  checker= false;
                  unselected.push(i)
              }
          }
      }
  }
  if(!checker){
      var first='';

      Array.from(document.getElementById('questions').children).forEach(el=> 
      {
          if(unselected.includes(parseInt(el.id.slice(9)))){
              el.classList.add('invalid')
              if(first===''){
                  first= el.id.slice(9)
              }
          }
          else{
              el.classList.remove("invalid")
          }
      })
      scrollToTarget('question-'+first)
  }
  return checker
}
window.onload = function() {
  Array.from(document.getElementsByClassName('zoom-image')).forEach(img => img.addEventListener('click',
      function() {
          document.getElementById("img").src = this.src;
          document.getElementById("imgModal").style.display = "block";
          document.getElementById("caption").innerHTML = this.alt
      }))
  let seconds = document.querySelector('#time').textContent;
  if (seconds.trim() != '∞') {
      seconds = 60 * parseInt(seconds.substr(0, seconds.indexOf(':'))) + parseInt(seconds.substr(seconds
          .indexOf(':') + 1))
      let display = document.querySelector('#time');
      startTimer(seconds, display);
  }
};