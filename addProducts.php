<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="./styles/addProducts.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-10 col-md-10 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                    <h2 id="heading">Add Products</h2>
                    <p>Fill all form fields to go to the next step</p>
                    <form id="msform" action="add_product_db.php" method="post" enctype="multipart/form-data">
                        <!-- progressbar -->
                        <ul id="progressbar">
                            <li class="active" id="account"><strong>Basic Information</strong></li>
                            <li id="personal"><strong>Pricing & Ratings</strong></li>
                            <li id="payment"><strong>Specifications</strong></li>
                            <li id="confirm"><strong>Finish</strong></li>
                        </ul>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div> <br>
                        <!-- fieldsets -->
                        <fieldset>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="fs-title">Basic Details:</h2>
                                    </div>
                                    <div class="col-5">
                                        <h2 class="steps">Step 1 - 4</h2>
                                    </div>
                                </div>
                                <label class="fieldlabels">Product Name: </label>
                                <input type="text" name="product_name" required />
                                <label class="fieldlabels">Brand: </label>
                                <input type="text" name="brand" required />
                                <label class="fieldlabels">Upload Product Images:</label>
                                <input type="file" name="product_images[]" accept="image/*" multiple class="formbold-form-input" />
                                <label class="fieldlabels">Image URLs (separate multiple URLs with commas):</label>
                                <textarea name="image_urls" rows="4" class="formbold-form-input" placeholder="Enter URLs separated by commas"></textarea>
                                <label class="fieldlabels">Product Description: </label>
                                <textarea id="product_description" name="product_description" class="formbold-form-input" rows="4"></textarea>
                            </div>
                            <input type="button" name="next" class="next action-button" value="Next" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="fs-title">Pricing & Ratings:</h2>
                                    </div>
                                    <div class="col-5">
                                        <h2 class="steps">Step 2 - 4</h2>
                                    </div>
                                </div>
                                <label class="fieldlabels">Old Price: </label>
                                <input type="number" name="old_price" step="0.01" required />
                                <label class="fieldlabels">New Price: </label>
                                <input type="number" name="new_price" step="0.01" required />
                                <label class="fieldlabels">Average Rating: </label>
                                <input type="number" name="average_rating" step="0.1" required />
                                <label class="fieldlabels">Number of Ratings: </label>
                                <input type="number" name="number_of_ratings" required />
                                <label class="fieldlabels">Number of Buyers: </label>
                                <input type="number" name="number_of_buyers" required />
                            </div>
                            <input type="button" name="next" class="next action-button" value="Next" />
                            <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="fs-title">Product Specifications:</h2>
                                    </div>
                                    <div class="col-5">
                                        <h2 class="steps">Step 3 - 4</h2>
                                    </div>
                                </div>
                                <label class="fieldlabels">Operating System:</label>
                                <input type="text" name="operating_system" />
                                <label class="fieldlabels">Memory Storage Capacity:</label>
                                <input type="text" name="memory_storage_capacity" />
                                <label class="fieldlabels">Screen Size:</label>
                                <input type="text" name="screen_size" />
                                <label class="fieldlabels">Resolution:</label>
                                <input type="text" name="resolution" />
                                <label class="fieldlabels">Color:</label>
                                <input type="text" name="color" />
                                <label class="fieldlabels">Quantity:</label>
                                <input type="number" name="quantity" value="100" />
                                <label class="fieldlabels">RAM:</label>
                                <input type="text" name="ram" />
                                <label class="fieldlabels">Processor Type:</label>
                                <input type="text" name="processor_type" />
                                <label class="fieldlabels">Processor:</label>
                                <input type="text" name="processor" />
                                <label class="fieldlabels">Network:</label>
                                <input type="text" name="network" />
                                <label class="fieldlabels">Rear Camera:</label>
                                <input type="text" name="rearCamera" />
                                <label class="fieldlabels">Front Camera:</label>
                                <input type="text" name="frontCamera" />
                                <label class="fieldlabels">Battery Capacity:</label>
                                <input type="text" name="battery_capacity" />
                            </div>
                            <input type="button" name="next" class="next action-button" value="Next" />
                            <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="fs-title">Finish:</h2>
                                    </div>
                                    <div class="col-5">
                                        <h2 class="steps">Step 4 - 4</h2>
                                    </div>
                                </div>
                                <h2 class="purple-text text-center"><strong>Confirmation!</strong></h2>
                                <p class="text-center">You are about to submit your product details. Please confirm to proceed.</p> <!-- Updated message -->
                            </div>
                            <input type="submit" name="submit" class="action-button" value="Submit" />
                            <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            var current_fs, next_fs, previous_fs;
            var opacity;
            var current = 1;
            var steps = $("fieldset").length;

            setProgressBar(current);

            $(".next").click(function() {
                current_fs = $(this).parent();
                next_fs = $(this).parent().next();

                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                next_fs.show();
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now) {
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        next_fs.css({
                            'opacity': opacity
                        });
                    },
                    duration: 600
                });
                setProgressBar(++current);
            });

            $(".previous").click(function() {
                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();

                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

                previous_fs.show();

                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now) {
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        previous_fs.css({
                            'opacity': opacity
                        });
                    },
                    duration: 600
                });
                setProgressBar(--current);
            });

            function setProgressBar(curStep) {
                var percent = parseFloat(100 / steps) * curStep;
                percent = percent.toFixed();
                $(".progress-bar").css("width", percent + "%");
            }

            $("#msform").submit(function(e) {
                var imageInput = $("input[name='productImages[]']");
                var imageUrlTextarea = $("textarea[name='imageUrls']");
                var imageInputHasFiles = imageInput[0].files.length > 0;
                var imageUrlTextareaNotEmpty = imageUrlTextarea.val().trim() !== "";

                if (!imageInputHasFiles && !imageUrlTextareaNotEmpty) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please upload product images or provide image URLs!',
                    });
                }
            });
        });
    </script>
</body>

</html>