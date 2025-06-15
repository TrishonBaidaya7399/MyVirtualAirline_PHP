<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php
include 'lib/functions.php';
include 'config.php';
session_start();
?>
<?php include 'includes/header.php';?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="jumbotron">
                <h1 class="text-center">About Us</h1>
                <img src="<?php echo website_base_url; ?>images/about-img.png" style="float:right; padding:15px;"
                    class="img-circle" />
                Delta Virtual, founded in 2019, has a commitment to building a worldwide virtual pilot community born not only of an appreciation of Delta Airlines, but also an understanding that what makes a virtual airline truly worthwhile is the bridges it can build between people.
				<br /><br />
				We value all those who embrace share this spirit and want to be a part of creating this kind of culture, sharing their talents to help build on what we've started here. <br /><br />
				At Delta Virtual, we believe that being part of a virtual airline is not just about flying on your computer from one place to another under a name, it’s about being part of a community. It’s about the conversations with other pilots from a whole other part of the country or part of the world, while you’re flying. It’s about a love of flight that brings us together. 
<br /><br />
So, are you ready? Great! Let’s Fly!
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';