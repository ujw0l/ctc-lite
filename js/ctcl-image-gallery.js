
/*
 * Ctcl Image Gallery
 * javascript library create gallery with main image
 * https://ujw0l.github.io/
 * MIT license
 *  
 */
'use strict'
class ctclImgGal{

    /**
     * Construtor 
     * 
     * @param {*} elems elements to apply gallery
     * @param {*} opt gallery option
     */
        constructor(elems,opt){
    
            Array.from(document.querySelectorAll(elems)).map(x=>{
                this.createGal(x,opt)});
    
        }
    
    /**
     * 
     * Create Gallery
     * 
     * @param {*} el gallery element
     * @param {*} opt gallery options
     */
        createGal(el,opt){
    
            let imgChngEvnt = undefined != opt && undefined != opt.imageEvent ? opt.imageEvent : 'click';
            let galWd = undefined != opt && undefined != opt.mainImgWd ? opt.mainImgWd : el.offsetWidth;
            let galHt = undefined != opt && undefined != opt.mainImgHt ? opt.mainImgHt : el.offsetHeight;
    
    
            let evryTngCont = document.createElement('div');
            evryTngCont.classList.add('ctclig-image-list');
            el.appendChild(evryTngCont);
    
            let mainImgDiv =  document.createElement('div');
            mainImgDiv.classList.add('ctclig-main-image');
            mainImgDiv.style = `width:${galWd}px; height :${galHt}px ;background: url("") center center / contain no-repeat rgb(255, 255, 255,0.5); margin-left:auto;margin-right:auto;display: block;`;
           mainImgDiv.innerHTML = `<span style='font-size:${galWd/30}px;position:relative; top: ${(galHt/2)-5}px; left: ${(galWd/2)-5}px ;' >Loading ...</span>`; 
            evryTngCont.appendChild(mainImgDiv);
    
          
    
            let carouselDivCont = document.createElement('div')
            carouselDivCont.style.width = `${galWd}px`,
            carouselDivCont.classList.add('ctclig-image-cont');
            carouselDivCont.style.overflowX = 'auto';
            carouselDivCont.style.overflowY = "hidden"
            carouselDivCont.style.marginLeft = 'auto';
            carouselDivCont.style.marginRight = 'auto';
            carouselDivCont.style.display = 'block';
    
            let carouselDiv =  document.createElement('div');
            carouselDiv.style.width = `0px`;
            carouselDiv.style.marginLeft = 'auto';
            carouselDiv.style.marginRight = 'auto';
            carouselDiv.style.display = 'block';
        
            carouselDivCont.appendChild(carouselDiv);
    
            evryTngCont.appendChild(carouselDivCont);
    
            
                Array.from(el.querySelectorAll('img')).map((y,i) => {
    
    
                  y.style.display = 'none';
    
                  let img = new Image();
                
                  img.src = y.src
                
                  img.addEventListener('load',e =>{
    
                    
              
                        if('' != mainImgDiv.innerHTML){
                            mainImgDiv.innerHTML = '';
                            mainImgDiv.setAttribute('data-num',i);
                            mainImgDiv.style.backgroundImage = `url("${e.target.src}")`;
                        }
    
                      
                        let imgHtWdRatio = e.target.width/e.target.height;
                        let imgResizeWd = imgHtWdRatio * 70;
                        carouselDiv.style.width = `${parseFloat(carouselDiv.style.width)+imgResizeWd+4}px` 
    
                       e.target.style.width = `${imgResizeWd}px`;
                       e.target.setAttribute('data-img-num',i);
                       e.target.style.height = '70px';
                       e.target.style.margin = '2px';
                        e.target.style.display = '';
                        e.target.addEventListener(imgChngEvnt, e => {
                            mainImgDiv.setAttribute('data-num',i);
                            mainImgDiv.style.backgroundImage = `url("${e.target.src}")`;
                            e.target.scrollIntoView({ behavior: "smooth", block:'nearest', inline: "center" });
                        });
    
                        carouselDiv.appendChild(e.target)
                    });
                });
    
           
                el.style.height = `${galHt +  100 }px`;
                undefined != opt && undefined != opt.callBack && opt.callBack(el);
        }
    
    }
    