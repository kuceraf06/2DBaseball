<?php
$pageTitle = "2D Baseball | Support";
$pageDescription = "Contact Support for questions or issues.";

$pageCSS = '<link rel="stylesheet" href="' . $baseUrl . 'public/css/support.css?v=' . time() . '">';
?>

<main class="main-content support-page">

    <section class="support-hero">
        <h1>Player <span>Support</span></h1>
        <p>Need help or have a question? Reach out to us via email.</p>
    </section>

    <section class="support-content">

       <div class="support-form-card">
            <h2>Contact Form</h2>

            <p class="contact-email">Or reach us directly at: <a href="mailto:2dbaseball25@gmail.com" target="_blank">2dbaseball25@gmail.com</a></p>

            <form id="contactForm" method="POST">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Your name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Your email" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Your message" rows="6" required></textarea>

                <button type="submit">Send</button>
            </form>
            <p id="formMessage" class="form-message"></p>
        </div>

    </section>

</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    emailjs.init("WeVjVTeOurYPIen1X");

    const form = document.getElementById("contactForm");
    const msg = document.getElementById("formMessage");

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = {
            name: form.querySelector("[name='name']").value,
            email: form.querySelector("[name='email']").value,
            message: form.querySelector("[name='message']").value
        };

        const sendToUser = emailjs.send(
            "service_spaf2bg",
            "template_dc2i7hv",
            formData
        );

        const sendToAdmin = emailjs.send(
            "service_spaf2bg",
            "template_emve7fc",
            formData
        );

        Promise.all([sendToUser, sendToAdmin])
            .then(() => {
                msg.textContent = "Message sent successfully!";
                msg.style.color = "#4BB543";
                form.reset();
            })
            .catch(() => {
                msg.textContent = "Something went wrong. Try again later.";
                msg.style.color = "red";
            });
    });
});
</script>

