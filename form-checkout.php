<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @package          WooCommerce/Templates
 * @version          3.5.0
 * @flatsome-version 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wrapper_classes = array();
$row_classes     = array();
$main_classes    = array();
$sidebar_classes = array();

$layout = get_theme_mod( 'checkout_layout' );

if ( ! $layout ) {
	$sidebar_classes[] = 'has-border';
}

if ( $layout == 'simple' ) {
	$sidebar_classes[] = 'is-well';
}

$wrapper_classes = implode( ' ', $wrapper_classes );
$row_classes     = implode( ' ', $row_classes );
$main_classes    = implode( ' ', $main_classes );
$sidebar_classes = implode( ' ', $sidebar_classes );

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

// Social login.
if ( flatsome_option( 'facebook_login_checkout' ) && get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' && ! is_user_logged_in() ) {
	wc_get_template( 'checkout/social-login.php' );
}
?>
<style>
.row.pt-0.custom_checkout {
    width: 50%;
    display: block;
    margin: 0px auto;
    background: #fbfafa;
    box-shadow: -1px 3px 20px 0px #c8bdbd;
	border-radius: 10px;
}
button#place_order {
    width: 100%;
    border-radius: 10px;
    padding: 7px;
}
.large-12.col.product_list_session {
    padding: 20px;
}
.custom_checkout span.select2-selection.select2-selection--single {
    border-radius: 8px;
}
.woocommerce-checkout-review-order-table tr.cart_item {
    display: none;
}
.message-container.container.medium-text-center {
    text-align: center;
}
a.icon-remove, a.remove {
    border: unset;
    border-radius: 100%;
    color: #000;
    display: block;
    font-size: 30px!important;
    font-weight: 700;
    height: 24px;
    line-height: 19px!important;
    text-align: center;
    width: 24px;
    position: relative;
    top: 6px;
}
div#customer_details input {
    padding: 20px;
    border-radius: 10px;
}
@media only screen and (max-width: 48em) {
	.row.pt-0.custom_checkout {
		width: 100%;
		display: block;
		margin: 0px auto;
		background: #fbfafa;
		box-shadow: -1px 3px 20px 0px #c8bdbd;
		border-radius: 10px;
	}	
}
</style>
<form name="checkout" method="post" class="checkout woocommerce-checkout <?php echo esc_attr( $wrapper_classes ); ?>" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<div class="row pt-0 <?php echo esc_attr( $row_classes ); ?> custom_checkout">
		<div class="large-12 col product_list_session">
			<div class="col-inner">
			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-name" colspan="3"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
			
				
			</tr>
		</thead>
		<tbody>
		
				<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
						</td>

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}

						// Mobile price.
						?>
						
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
						
					
					

						
					</tr>
					<?php
				}
			}
			?>
			</tbody>
	</table>
			</div>
		</div>
		<?php /*
		<div class="large-12 col">
			<?php flatsome_sticky_column_open( 'checkout_sticky_sidebar' ); ?>

					<div class="col-inner <?php echo esc_attr( $sidebar_classes ); ?>">
						<div class="checkout-sidebar sm-touch-scroll">

							<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

							<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

							<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

							<div id="order_review" class="woocommerce-checkout-review-order">
								<?php // do_action( 'woocommerce_checkout_order_review' ); ?>
							</div>

							
						</div>
					</div>

			<?php flatsome_sticky_column_close( 'checkout_sticky_sidebar' ); ?>
			
		</div>
		*/?>
		<div class="large-12 col  <?php echo esc_attr( $main_classes ); ?>">
			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div id="customer_details">
					<div class="clear">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<div class="clear">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		

	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
