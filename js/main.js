let navigation = document.querySelector('.home');

document.querySelector('#collapse-side').onclick = () => {
  navigation.classList.toggle('collapse');
}

$("#edit_att_req").click(function(event){
  event.preventDefault();
  $('#disabledTextInput').prop("disabled", (i, v) => !v); // (i, v) => !v) is to toggle.
});

$("#edit_att_req2").click(function(event){
  event.preventDefault();
  $('#disabledTextInput2').prop("disabled", (i, v) => !v); // (i, v) => !v) is to toggle.
});