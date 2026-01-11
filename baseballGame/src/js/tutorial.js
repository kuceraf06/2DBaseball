document.addEventListener("DOMContentLoaded", () => {
  let tutorialIndex = 0;

  if (!openBtn || !tutorialModal) {
    return;
  }

  function updateTutorial() {
    tutorialImage.src = tutorialImages[tutorialIndex];
    tutorialCounter.textContent = `${tutorialIndex + 1} / ${tutorialImages.length}`;

    prevBtn.disabled = tutorialIndex === 0;
    nextBtn.disabled = tutorialIndex === tutorialImages.length - 1;
  }

  openBtn.addEventListener("click", () => {
    tutorialIndex = 0;
    updateTutorial();
    tutorialModal.classList.add("show");
  });

  closeBtn.addEventListener("click", () => {
    tutorialModal.classList.remove("show");
  });

  nextBtn.addEventListener("click", () => {
    if (tutorialIndex < tutorialImages.length - 1) {
      tutorialIndex++;
      updateTutorial();
    }
  });

  prevBtn.addEventListener("click", () => {
    if (tutorialIndex > 0) {
      tutorialIndex--;
      updateTutorial();
    }
  });
});
