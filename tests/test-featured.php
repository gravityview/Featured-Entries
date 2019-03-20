<?php
class GV_Advanced_Filter_Tests extends GV_UnitTestCase {
	private function _reset_context() {
		\GV\Mocks\Legacy_Context::reset();
		gravityview()->request = new \GV\Frontend_Request();

		remove_all_filters( 'gravityview/view/query' );

		global $post;
		$post = null;

		\GV\View::_flush_cache();

		set_current_screen( 'front' );
		wp_set_current_user( 0 );
	}

	private function get_entries( $form_id ) {
		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'One',
		) );
		$one = \GV\GF_Entry::by_id( $entry['id'] );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'Two',
		) );
		$two = \GV\GF_Entry::by_id( $entry['id'] );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'Three',
		) );
		$three = \GV\GF_Entry::by_id( $entry['id'] );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'Four',
		) );
		$four = \GV\GF_Entry::by_id( $entry['id'] );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'Five',
		) );
		$five = \GV\GF_Entry::by_id( $entry['id'] );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'Six',
		) );
		$six = \GV\GF_Entry::by_id( $entry['id'] );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status' => 'active',
			'1' => 'Seven',
		) );
		$seven = \GV\GF_Entry::by_id( $entry['id'] );

		return compact( 'one', 'two', 'three', 'four', 'five', 'six', 'seven' );
	}

	public function test_top_simple() {
		$this->_reset_context();

		$form = $this->factory->form->import_and_get( 'simple.json' );

		global $post;

		$post = $this->factory->view->create_and_get( array(
			'form_id' => $form['id'],
			'template_id' => 'table',
			'fields' => array(
				'directory_table-columns' => array(
					wp_generate_password( 4, false ) => array(
						'id' => '1',
						'label' => 'Entry number',
					),
				),
			),
		) );
		$view = \GV\View::from_post( $post );

		$entries = $this->get_entries( $form['id'] );

		gravityview()->request->returns['is_view'] = $view;
		$renderer = new \GV\View_Renderer();

		$this->assertCount( 7, $view->get_entries()->all() );
		
		$this->assertNotContains( 'class="featured"', $renderer->render( $view ) );

		\GFAPI::update_entry_property( $entries['three']->ID, 'is_starred', true );

		$this->assertContains( 'Three<span class="featured"></span>', $renderer->render( $view ) );

		$view->settings->update( array( 'featured_entries_to_top' => true ) );

		$entry_ids = wp_list_pluck( $view->get_entries()->all(), 'ID' );
		$this->assertEquals( $entries['three']->ID, reset( $entry_ids ) );

		$this->_reset_context();
	}

	public function test_top_pagination_sticky_mode() {
	}

	public function test_top_pagination_regular_mode() {
	}

	public function test_top_search() {
	}
}
