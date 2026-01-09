let gameScale = 1;

async function scaleGame() { 
    const wrapper = document.querySelector('.gameWrapper'); 
    const maxWidth = 1100; 
    const maxHeight = 810;

    let isFullscreen = false;

    if (window.api?.getWindowMode) {
        const mode = await window.api.getWindowMode();
        isFullscreen = mode === 'fullscreen';
    }

    const scaleX = window.innerWidth / maxWidth; 
    const scaleY = window.innerHeight / maxHeight; 

    gameScale = isFullscreen
    ? Math.min(scaleX, scaleY)
    : Math.min(scaleX, scaleY, 1);

    wrapper.style.transform = `scale(${gameScale})`;
    wrapper.style.width = `${maxWidth}px`;
    wrapper.style.height = `${maxHeight}px`;
}

window.addEventListener('load', scaleGame); 
window.addEventListener('resize', scaleGame);

window.api?.onWindowModeChanged(() => {
  scaleGame();
});