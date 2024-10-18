const showToast = (message, error = false, duration = 2000) => {
  let box = document.createElement("div");
  box.classList.add("toast");
  if (error) {
    box.classList.add("error");
  }
  box.innerHTML = ` <div class="toast-content-wrapper"> 
                      <div class="toast-message">${message}</div> 
                      <div class="toast-progress"></div> 
                      </div>`;
  duration = duration || 5000;
  box.querySelector(".toast-progress").style.animationDuration = `${
    duration / 1000
  }s`;

  let toastAlready = document.body.querySelector(".toast");
  if (toastAlready) {
    toastAlready.remove();
  }

  document.body.appendChild(box);
};
