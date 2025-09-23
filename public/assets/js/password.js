<<<<<<< HEAD
document.addEventListener("DOMContentLoaded", function() {
=======
document.addEventListener("DOMContentLoaded", function () {
>>>>>>> base_v3
    var showHideElements = document.querySelectorAll(".show-hide");
    // var passwordInput = document.querySelector('input[name="login[password]"]');
    var passwordInput = document.querySelector('input[name="password"]');
    var showHideSpan = document.querySelector(".show-hide span");
    var submitButton = document.querySelector('form button[type="submit"]');
<<<<<<< HEAD
  
    showHideElements.forEach(function(element) {
      element.style.display = "block";
    });
  
    showHideSpan.classList.add("show");
  
    showHideSpan.addEventListener("click", function() {
      if (showHideSpan.classList.contains("show")) {
        passwordInput.setAttribute("type", "text");
        showHideSpan.classList.remove("show");
      } else {
        passwordInput.setAttribute("type", "password");
        showHideSpan.classList.add("show");
      }
    });
  
    submitButton.addEventListener("click", function() {
      showHideSpan.classList.add("show");
      passwordInput.setAttribute("type", "password");
    });
  });
=======

    showHideElements.forEach(function (element) {
        element.style.display = "block";
    });

    if (showHideSpan) {
        showHideSpan.classList.add("show");
    }

    if (showHideSpan) {
        showHideSpan.addEventListener("click", function () {
            if (showHideSpan.classList.contains("show")) {
                passwordInput.setAttribute("type", "text");
                showHideSpan.classList.remove("show");
            } else {
                passwordInput.setAttribute("type", "password");
                showHideSpan.classList.add("show");
            }
        });
    }

    submitButton.addEventListener("click", function () {
        if (showHideSpan) {
            showHideSpan.classList.add("show");
        }
        passwordInput.setAttribute("type", "password");
    });
});
>>>>>>> base_v3
