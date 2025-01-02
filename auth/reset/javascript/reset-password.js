const form = document.querySelector(".reset form"),
continueBtn = form.querySelector(".button button"),
errorText = form.querySelector(".error-text");
const redirectContainer = document.querySelector('.redirect-container')


form.onsubmit = (e)=>{
    e.preventDefault();
}

continueBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/reset-password.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              if(data === "success"){
                form.style.display = "none";
                redirectContainer.style.display = "block";
                setTimeout(()=>{
                  location.href = "../../console";
                },2500)
              }else{
                errorText.style.display = "block";
                errorText.textContent = data;
              }
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}