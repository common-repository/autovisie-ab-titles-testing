<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.autovisie.nl
 * @since      1.0.0
 *
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/public/partials
 */
$use_js = esc_html( get_option( 'av_ab_testing_use_js' ) );
?>

<?php if( $use_js && !is_404() ): ?>
	<script type="text/javascript">
		(function( $ ) {
			$(document).ready(function(){
				/**
				 * Get the title
				 */
				var excludedClasses = $('body').hasClass('home', 'category', 'archive'),
					singleItem = $('body').hasClass('single', 'page'),
					postIds = [];

				if(ab_hide_titles){
					$('.ab-title-box').css({
						position: 'relative',
						left: '-9999px'
					});
				}

				$('.ab-title-box').each(function(){
					var current_post_id = $(this).attr('data-id');
					postIds.push($(this).attr('data-id'));
				});

				$.ajax({
					type : "post",
					dataType : "json",
					url : av_ab_test.ajax_url,
					data : {action: "av_ab_test_get_ajax_titles", post_ids: uniqueArray(postIds), single_item: singleItem}
				}).done(function( data ){
					if(data){
						var spanId,
							titleValue,
							ogTitle,
							ogTitleValue;

						$.each(data, function(key, value){
							spanId = $(".ab-title-" + key);
							titleValue = decodeEntities(value);
							ogTitle = $("meta[property='og\\:title']");
							ogTitleValue = ogTitle.attr('content');
							if(spanId.length){
								spanId.text(titleValue);
							}

							if(!excludedClasses && key === ab_post_id){
								if(ogTitleValue.length){
									ogTitle.attr('content', titleValue);
								}

								$(document).attr("title", titleValue);
							}
						});
					}

					if(ab_hide_titles){
						$('.ab-title-box').css({
							position: 'relative',
							left: '0'
						});
					}
				});
			});

			function uniqueArray(array) {
				return $.grep(array, function(el, index) {
					return index === $.inArray(el, array);
				});
			}

			function decodeEntities(encodedString) {
				var textArea = document.createElement('textarea');
				textArea.innerHTML = encodedString;
				return textArea.value;
			}
		})( jQuery );
	</script>
<?php endif; ?>