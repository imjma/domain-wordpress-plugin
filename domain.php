<?php
/**
 * @package Domain
 */
/*
Plugin Name: Domain
Description: A Domain Plugin
Version: 0.1
AuthorL Jie Ma
 */

require 'vendor/autoload.php';

function show_listing($atts) {
    $id = $atts['id'] ?: false;
    // if no id, return empty
    if ($id === false) {
        return '';
    }

    // if defined default title
    $title = $atts['title'] ?: 'Listing';

    // init guzzle
    $client = new GuzzleHttp\Client();

    // get json data
    $res = $client->request('GET', 'http://rest.domain.com.au/propertydetailsservice.svc/propertydetail/' . $id);

    // if get the data
    if ($res->getStatusCode() == '200') {
        $content = json_decode($res->getBody(), true);
        $listing = $content['Listings'][0];

        // display_price
        $display_price = '';
        // if ShowPrice is false, hide price
        if ($listing['Instruction']['Price']['ShowPrice'] != "false") {
            $display_price = $listing['Instruction']['Price']['DisplayPrice'];
        }

        // define listing labels
        // http://style.domain.com.au/web/components/labels.html
        $listing_label = false;
        switch($listing['Status']) {
        case 'Under offer':
            $listing_label = ' label-offer';
            break;
        case 'Sold':
            $listing_label = ' label-sold';
            break;
        case 'Updated':
            $listing_label = ' label-updated';
            break;
        case 'New':
            $listing_label = ' label-new';
            break;
        case 'New Homes':
            $listing_label = ' label-section';
            break;
        case 'Auction':
            $listing_label = ' label-auction';
            break;
        }
?>
<div class="row">
        <div class="large-6 columns">
            <!-- Listing Card START -->
            <div class="card listing clickable">
                <a href="javascript:;">
                    <div class="media-wrap crop-image">
<?php
        // if listing has image
        if (isset($listing['Property']['Images'][0]['RetinaDisplayThumbUrl'])) {
?>
    <img src="<?php echo $listing['Property']['Images'][0]['RetinaDisplayThumbUrl']; ?>">
<?php
        }
?>
                        <!-- UI NOTE: image-placeholder should be always used as a fallback -->
                        <div class="image-placeholder bg-color">
                            <div class="image-loader-wrap with-icon compact">
                                <div class="image-loader"></div>
                                <span class="f-icon">
                                    <span class="icon domain-icon-ic_home"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="outer-wrap">
                    <div class="inner-wrap">
                        <a href="javascript:;">
<?php
        if ($listing_label) {
?>
                        <span class="status-label<?php echo $listing_label; ?>"><?php echo $listing['Status']; ?></span>
<?php
        }
?>
                            <div class="price-wrap">
                            <h4 class="truncate-single"><?php echo $display_price; ?></h4>
                            </div>
                            <ul class="features">
                                <li class="truncate-single">
<?php
        if (isset($listing['Property']['Features'])) {
            foreach ($listing['Property']['Features'] as $k => $feature) {
                if ($feature['Value'] == '') {
                    continue;
                }

                echo '<span>';
                if ($k > 0) {
                    echo ', ';
                }
                echo $feature['Value'] . '</span>';
                if ($feature['Name'] == 'Bedrooms') {
                    echo '<span> beds</span>';
                }
                if ($feature['Name'] == 'Bathrooms') {
                    echo '<span> baths</span>';
                }
                if ($feature['Name'] == 'Carspaces') {
                    echo '<span> parking</span>';
                }
            }
        }
?>
                                </li>
                                <li>
                                    <ul class="list-horizontal">
                                        <!-- UI NOTE: these must all be placed on the same line to avoid a space between list items -->
<?php
        if (!empty($listing['Property']['Area'])) {
?>
<li><?php echo $listing['Property']['Area']; ?></li>
<?php
        }
?>
<li><?php echo $listing['Property']['Type']; ?></li>
                                    </ul>
                                </li>
                            </ul>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Listing Card END -->
        </div>
        <div class="large-6 columns">
        <h3 class="dui"><?php echo $title; ?></h3>
        </div>
    </div>
<?php
    } else {
        return '';
    }
}
add_shortcode('domain', 'show_listing');
