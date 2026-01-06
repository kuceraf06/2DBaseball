document.addEventListener("DOMContentLoaded", () => {
  const tutorialImages = [
    "images/tutorial/1-step.png",
    "images/tutorial/2-step.png",
    "images/tutorial/3-step.png",
    "images/tutorial/4-step.png",
    "images/tutorial/5-step.png",
    "images/tutorial/6-step.png",
    "images/tutorial/7-step.png",
    "images/tutorial/8-step.png",
    "images/tutorial/9-step.png",
    "images/tutorial/10-step.png"
  ];

  let tutorialIndex = 0;

  const tutorialModal = document.getElementById("tutorialModal");
  const tutorialImage = document.getElementById("tutorialImage");
  const tutorialCounter = document.getElementById("tutorialCounter");

  const openBtn = document.getElementById("tutorial");
  const closeBtn = document.getElementById("closeTutorial");
  const nextBtn = document.getElementById("tutorialNext");
  const prevBtn = document.getElementById("tutorialPrev");

  if (!openBtn || !tutorialModal) {
    console.error("❌ Tutorial elements not found");
    return;
  }

  function updateTutorial() {
    tutorialImage.src = tutorialImages[tutorialIndex];
    tutorialCounter.textContent = `${tutorialIndex + 1} / ${tutorialImages.length}`;
  }

  openBtn.addEventListener("click", () => {
    tutorialIndex = 0;
    updateTutorial();
    tutorialModal.classList.add("show");
    console.log("✅ Tutorial opened");
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
