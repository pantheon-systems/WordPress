<?php

class OTGS_Installer_Package_Product_Finder {

	/**
	 * @param OTGS_Installer_Repository $repository
	 * @param OTGS_Installer_Subscription $subscription
	 *
	 * @return null|OTGS_Installer_Package_Product
	 */
	public function get_product_in_repository_by_subscription( OTGS_Installer_Repository $repository, OTGS_Installer_Subscription $subscription = null ) {
		$product = null;

		if ( ! $subscription ) {
			$subscription = $repository->get_subscription();
		}
		if ( $subscription ) {
			$type    = $subscription->get_type();
			$product = $repository->get_product_by_subscription_type( $type );

			if ( ! $product ) {
				$product = $repository->get_product_by_subscription_type_equivalent( $type );

				if ( ! $product ) {
					$product = $repository->get_product_by_subscription_type_on_upgrades( $type );
				}
			}
		}

		return $product;
	}
}