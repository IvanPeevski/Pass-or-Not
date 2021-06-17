//Create Tag
function createTag(label) {
  let div = document.createElement('div');
  div.setAttribute('class', 'tag');
  let span = document.createElement('span');
  span.innerHTML = label;
  let closeIcon = document.createElement('button');
  closeIcon.setAttribute('type', 'button')
  closeIcon.addEventListener('click', function(){ this.parentNode.parentNode.removeChild(this.parentNode)})
  closeIcon.innerHTML = '<span aria-hidden="true">&times;</span>';
  closeIcon.setAttribute('class', 'close tag-remove');
  closeIcon.setAttribute('aria-label', label);
  div.appendChild(span);
  div.appendChild(closeIcon);
  return div;
}

//clearTags
function clearTags() {
  document.querySelectorAll('.tag').forEach(tag => {
      tag.parentElement.removeChild(tag);
  });
}
//Add tags
function addTags(labels) {
  let tags  = Array.from(document.getElementsByClassName('tag')).map(tag => tag.firstChild.textContent)
  tags = tags.concat(labels)
  tags = tags.filter(tag=> tag!='')
  clearTags();
  tags  = tags.reverse().forEach(tag => {
      document.querySelector('.tag-container').prepend(createTag(tag));
  });
}
//Ready tags
function readyTags(){
  document.querySelector('.tag-container input').addEventListener('keyup', (e) => {
      if (e.key === 'Enter') {
          labels = e.target.value.split(',');
          addTags(labels);
          e.target.value = '';
      }
  })
}