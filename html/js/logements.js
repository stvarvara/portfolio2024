function handleResize() {
    const containerActifInactif = document.querySelectorAll('.actifInactif');
    const containerInfo = document.querySelectorAll('.conteneur_info');
    const containerActions = document.querySelectorAll(".actions");
    if (window.innerWidth <= 750) {
            for(let i=0;i<containerActions.length;i++){
                containerActions[i].appendChild(containerActifInactif[i])
            }    
       }
     else {
        for(let i=0;i<containerInfo.length;i++){
            containerInfo[i].appendChild(containerActifInactif[i])
        }
    }
}
window.addEventListener('resize', handleResize);
window.addEventListener('load', handleResize);


document.addEventListener('DOMContentLoaded', (event) => {
    const message = sessionStorage.getItem('message');


if(message){
    const messageEdit = document.getElementById("messageEdit");
    messageEdit.textContent = message;

    const divMessage = document.querySelector(".alert")
    divMessage.classList.remove('hidden');
    divMessage.classList.add('flex');

    divMessage.classList.remove('hidden');
        setTimeout(() => {
          messageDiv.classList.add('fade-in');
        }, 100);


        setTimeout(() => {
            divMessage.classList.remove('fade-in');
            setTimeout(() => {
              divMessage.classList.add('hidden');
            }, 1000); // Correspond au délai de transition CSS
          }, 3500); // 5 secondes avant de commencer la disparition
        

    sessionStorage.removeItem('message');


}

})

