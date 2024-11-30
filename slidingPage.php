<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <title>Image Slideshow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fff;
        }

        .slideshow-container {
            position: relative;
            margin-top: 50px;
            max-width: 100%;
            overflow: hidden;
            background-color: #fff;
        }

        .slides {
            display: none;
            text-align: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }

        .slide-image {
            width: 70%;
            margin: auto;
            border-radius: 10px;
            height: 300px;
        }

        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            padding: 16px;
            margin-top: -22px;
            color: black;
            font-weight: bold;
            font-size: 18px;
            user-select: none;
            border: none;
            border-radius: 5px;
            z-index: 1;
            background-color: transparent;
            transition: background-color 0.3s ease;
        }

        .next {
            right: 10px;
        }

        .prev {
            left: 10px;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .dot {
            cursor: pointer;
            height: 12px;
            width: 12px;
            margin: 0 4px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }

        .active,
        .dot:hover {
            background-color: #717171;
        }

        .text-center {
            text-align: center;
        }

        .dots-container {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="slideshow-container">
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1718627733_471.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1716983001_233.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1718962950_477.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1719479234_479.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1727784815_540.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1718877434_476.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1723460430_502.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1724834579_509.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1720783066_490.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1721212417_488.jpg" class="slide-image">
        </div>
        <div class="slides">
            <img src="https://assets.sangeethamobiles.com/placeholder_banner/placeholderBanner_1722239347_489.jpg" class="slide-image">
        </div>
        <!-- Next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>

    <br>

    <!-- The dots/circles -->
    <div class="dots-container">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
        <span class="dot" onclick="currentSlide(4)"></span>
        <span class="dot" onclick="currentSlide(5)"></span>
        <span class="dot" onclick="currentSlide(6)"></span>
        <span class="dot" onclick="currentSlide(7)"></span>
        <span class="dot" onclick="currentSlide(8)"></span>
        <span class="dot" onclick="currentSlide(9)"></span>
        <span class="dot" onclick="currentSlide(10)"></span>
        <span class="dot" onclick="currentSlide(11)"></span>
    </div>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        // Automatically move to the next slide every 4 seconds
        setInterval(function() {
            plusSlides(1);
        }, 4000);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("slides");
            let dots = document.getElementsByClassName("dot");
            if (n > slides.length) {
                slideIndex = 1;
            }
            if (n < 1) {
                slideIndex = slides.length;
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
                slides[i].style.opacity = 0;
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex - 1].style.display = "block";
            slides[slideIndex - 1].style.opacity = 1;
            dots[slideIndex - 1].className += " active";
        }
    </script>
</body>

</html>