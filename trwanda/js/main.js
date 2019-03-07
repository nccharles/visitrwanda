window.addEventListener('scroll', function() {
console.log(window.scrollY)
if(window.scrollY>=500){
    // document.querySelector("scrolled").style.display='block'
    document.querySelector('#scrolled').classList.add('scrolled')
}else{
    // document.querySelector("scrolled").style.display='none'
    document.querySelector('#scrolled').classList.remove('scrolled')
}

});