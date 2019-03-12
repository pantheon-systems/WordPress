<?php

class OTGS_Installer_Repositories {

	private $installer;
	private $repositories;
	private $repository_factory;
	private $subscription_factory;

	public function __construct(
		WP_Installer $installer,
		OTGS_Installer_Repository_Factory $repository_factory,
		OTGS_Installer_Subscription_Factory $subscription_factory
	) {
		$this->repository_factory   = $repository_factory;
		$this->subscription_factory = $subscription_factory;
		$this->installer            = $installer;
		$settings                   = $this->installer->get_settings();
		$this->repositories         = $this->get_repositories( $settings['repositories'] );
	}

	public function get_all() {
		return $this->repositories;
	}

	private function get_repositories( $setting_repositories ) {
		$repositories = array();

		foreach ( $setting_repositories as $id => $repository ) {
			$subscription = isset( $repository['subscription']['data'] )
				? $this->subscription_factory->create( $repository['subscription'] )
				: null;

			$setting_repositories = $this->installer->get_repositories();

			$packages             = $this->get_packages( $repository );
			$repositories[] = $this->repository_factory->create_repository( array(
					'id'            => $id,
					'subscription'  => $subscription,
					'packages'      => $packages,
					'product_name'  => $repository['data']['product-name'],
					'api_url'       => $setting_repositories[ $id ]['api-url'],
					'products_url' => $setting_repositories[ $id ]['products'],
				)
			);
		}

		return $repositories;
	}

	private function get_packages( $repository ) {
		$packages = array();

		foreach ( $repository['data']['packages'] as $package_key => $package ) {
			$products = $this->get_products( $package );

			$packages[] = $this->repository_factory->create_package( array(
				'key'         => $package_key,
				'id'          => $package['id'],
				'name'        => $package['name'],
				'description' => $package['description'],
				'image_url'   => $package['image_url'],
				'order'       => $package['order'],
				'parent'      => $package['parent'],
				'products'    => $products,
			) );
		}

		return $packages;
	}

	private function get_products( $package ) {
		$products = array();

		foreach ( $package['products'] as $product_key => $product ) {
			$products[] = $this->repository_factory->create_product( array(
				'id'                           => $product_key,
				'name'                         => $product['name'],
				'description'                  => $product['description'],
				'price'                        => $product['price'],
				'subscription_type'            => $product['subscription_type'],
				'subscription_type_text'       => $product['subscription_type_text'],
				'subscription_info'            => $product['subscription_info'],
				'subscription_type_equivalent' => $product['subscription_type_equivalent'],
				'url'                          => $product['url'],
				'renewals'                     => $product['renewals'],
				'upgrades'                     => $product['upgrades'],
				'plugins'                      => $product['plugins'],
				'downloads'                    => $product['downloads'],
			) );
		}

		return $products;
	}

	/**
	 * @param $id
	 *
	 * @return null|OTGS_Installer_Repository
	 */
	public function get( $id ) {
		foreach ( $this->repositories as $repository ) {
			if ( $id === $repository->get_id() ) {
				return $repository;
			}
		}

		return null;
	}

	public function refresh() {
		$this->installer->refresh_repositories_data();
	}

	public function save_subscription( OTGS_Installer_Repository $repository ) {
		$subscription = $repository->get_subscription();
		unset( $this->installer->settings['repositories'][ $repository->get_id() ]['subscription'] );

		if ( $subscription ) {
			$this->installer->settings['repositories'][ $repository->get_id() ]['subscription'] = array(
				'key'           => $subscription->get_site_key(),
				'data'          => $subscription->get_data(),
				'registered_by' => $subscription->get_registered_by(),
				'site_url'      => $subscription->get_site_url(),
			);
		}

		$this->installer->save_settings();
	}
}