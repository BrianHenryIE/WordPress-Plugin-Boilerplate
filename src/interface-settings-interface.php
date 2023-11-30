<?php


namespace Plugin_Package_Name;

interface Settings_Interface {

	public function get_plugin_name(): string;

	public function get_plugin_version(): string;

	public function get_plugin_slug(): string;

	public function get_plugin_basename(): string;

	public function get_log_level(): string;
}
