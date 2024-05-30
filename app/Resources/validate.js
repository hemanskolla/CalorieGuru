function validate(formObj) {
    console.log(formObj)
    if (formObj.Username.value == "") {
      alert("Please enter a username");
      formObj.Username.focus();
      return false;
    }
    
    if (formObj.Password.value == "") {
      alert("Please enter a Passowrd");
      formObj.Password.focus();
      return false;
    }
    return true;
}