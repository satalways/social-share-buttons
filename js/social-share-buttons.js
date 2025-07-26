/**
 * Social Share Buttons JavaScript
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initSocialShareButtons();
    });

    /**
     * Initialize social share buttons
     */
    function initSocialShareButtons() {
        // Handle click events on share buttons
        $('.ssb-button').on('click', function(e) {
            var href = $(this).attr('href');
            
            // Don't open popups for email links
            if (href.indexOf('mailto:') === 0) {
                return true;
            }
            
            e.preventDefault();
            
            // Open share dialog in a popup window
            openSharePopup(href);
            
            // Track share event if analytics is available
            trackShareEvent($(this));
            
            return false;
        });
    }

    /**
     * Open share dialog in a popup window
     * 
     * @param {string} url The URL to open in the popup
     */
    function openSharePopup(url) {
        var width = 600;
        var height = 400;
        var left = (window.innerWidth - width) / 2;
        var top = (window.innerHeight - height) / 2;
        
        window.open(
            url,
            'share-dialog',
            'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1'
        );
    }

    /**
     * Track share event using analytics if available
     * 
     * @param {jQuery} $button The button element that was clicked
     */
    function trackShareEvent($button) {
        var network = '';
        
        // Determine which network was shared to
        if ($button.closest('.ssb-facebook').length) {
            network = 'Facebook';
        } else if ($button.closest('.ssb-twitter').length) {
            network = 'Twitter';
        } else if ($button.closest('.ssb-linkedin').length) {
            network = 'LinkedIn';
        } else if ($button.closest('.ssb-pinterest').length) {
            network = 'Pinterest';
        } else if ($button.closest('.ssb-reddit').length) {
            network = 'Reddit';
        } else if ($button.closest('.ssb-email').length) {
            network = 'Email';
        }
        
        // Track with Google Analytics if available
        if (typeof ga !== 'undefined') {
            ga('send', 'event', 'Social Share', 'Share', network);
        }
        
        // Track with Google Tag Manager if available
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({
                'event': 'socialShare',
                'socialNetwork': network,
                'socialAction': 'Share',
                'socialTarget': window.location.href
            });
        }
    }

    /**
     * Copy current URL to clipboard
     * 
     * @param {Event} e Click event
     */
    function copyToClipboard(e) {
        e.preventDefault();
        
        var tempInput = document.createElement('input');
        tempInput.value = window.location.href;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Show success message
        var $button = $(e.target).closest('.ssb-copy');
        var originalText = $button.find('.ssb-text').text();
        
        $button.find('.ssb-text').text('Copied!');
        
        setTimeout(function() {
            $button.find('.ssb-text').text(originalText);
        }, 2000);
        
        return false;
    }

})(jQuery);