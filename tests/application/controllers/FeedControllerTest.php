<?php

class FeedControllerTest extends ControllerTestCase
{
	public function testVisitSubmit()
	{
		$this->dispatch("/feed/submit");
		$this->assertController("feed");
		$this->assertAction("submit");
		$this->assertResponseCode(200);
	}
}