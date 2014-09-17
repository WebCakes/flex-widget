<?php

/**
 *
 */
class Test_Flex_Widget_Plugin extends WP_UnitTestCase {

	/**
	 *
	 */
	public function test_image_size_names() {
		$sizes = Flex_Widget_Plugin::get_image_size_names();
		$this->assertEquals( 4, count( $sizes ) );
		$this->assertEquals( 'Thumbnail', $sizes['thumbnail'] );
	}

}
