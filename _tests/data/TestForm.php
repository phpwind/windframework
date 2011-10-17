<?php
class TestForm extends WindEnableValidateModule {
	
	private $shi;
	private $long;

	
	
	/**
	 * @return field_type
	 */
	public function getShi() {
		return $this->shi;
	}

	/**
	 * @return field_type
	 */
	public function getLong() {
		return $this->long;
	}

	/**
	 * @param field_type $shi
	 */
	public function setShi($shi) {
		$this->shi = $shi;
	}

	/**
	 * @param field_type $long
	 */
	public function setLong($long) {
		$this->long = $long;
	}

	/* (non-PHPdoc)
	 * @see WindEnableValidateModule::validateRules()
	 */
	public function validateRules() {
		return array(
			WindUtility::buildValidateRule("shi", "isRequired"), 
			WindUtility::buildValidateRule("long", "isRequired"));
	}

}