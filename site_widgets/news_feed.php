<?php

use Proxy\Api\Api;

Api::__constructStatic();
$obj = null;
$res = Api::sendSync('GET', 'v1/news/latest', null);
if ($res->getStatusCode() == 200) {
    $obj = json_decode($res->getBody(), false);
}
?>

        <div class="col-12">
            <div class="global-heading">
                <h3 class="global-title">NOTAMs</h3>
            </div>
            <div class="glass-card">
                <div class="">
                    <?php if (!empty($obj)) { ?>
                        <?php foreach ($obj as $key => $news) { ?>
                            <div class="col-12">
                                <i class="fa fa-bullhorn" aria-hidden="true"></i> <a href="<?php echo website_base_url; ?>news_item.php?id=<?php echo $news->id; ?>" class="news-link">
                                    <?php echo $news->title; ?>
                                </a><br />
                                <span class="news-posted-by">posted by <?php echo $news->poster; ?>
                                    on <?php echo (new DateTime($news->date))->format('d M Y'); ?></span><br /><br />
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>There are no news articles.</p>
                    <?php } ?>
                </div>
            </div>
        </div>


<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Glassmorphism Card */
.glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 20px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Panel Heading */
.panel-heading {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 15px;
    border-bottom: 1px solid rgba(255, 215, 0, 1);
}

.panel-title {
    color: #fff;
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Panel Body */
.panel-body {
    padding: 20px;
    color: #fff;
}

.news-link {
    color: #fff;
    text-decoration: none;
    font-size: 1.2rem;
}

.news-link:hover {
    color: #ffd700;
    text-decoration: underline;
}

.news-posted-by {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
}
/* Global Heading */
.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
    }
}
/* Responsive Design */
@media (max-width: 1200px) {
    .panel-body {
        padding: 15px;
    }
    .news-link {
        font-size: 1.1rem;
    }
    .news-posted-by {
        font-size: 1.1rem;
    }
}

@media (max-width: 992px) {
    .panel-title {
        font-size: 1.5rem;
    }
    .news-link {
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    .panel-title {
        font-size: 1.3rem;
    }
    .news-link {
        font-size: 0.9rem;
    }
    .news-posted-by {
        font-size: 1rem;
    }
    .panel-body {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .panel-title {
        font-size: 1.2rem;
    }
    .news-link {
        font-size: 0.8rem;
    }
    .panel-body {
        padding: 8px;
    }
}

/* Print Styles */
@media print {
    .glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .panel-title, .panel-body, .news-link, .news-posted-by {
        color: black;
        text-shadow: none;
    }
}