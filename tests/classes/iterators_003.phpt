--TEST--
ZE2 iterators and break
--SKIPIF--
<?php if (version_compare(zend_version(), '2.0.0-dev', '<')) die('skip ZendEngine 2 needed'); ?>
<?php if (!class_exists('Iterator')) print "skip interface iterator doesn't exist"; ?>
--FILE--
<?php
class c_iter implements Iterator {

	private $obj;
	private $num = 0;

	function __construct($obj) {
		echo __METHOD__ . "\n";
		$this->obj = $obj;
	}
	function rewind() {
		echo __METHOD__ . "\n";
	}
	function hasMore() {
		$more = $this->num < $this->obj->max;
		echo __METHOD__ . ' = ' .($more ? 'true' : 'false') . "\n";
		return $more;
	}
	function current() {
		echo __METHOD__ . "\n";
		return $this->num;
	}
	function next() {
		echo __METHOD__ . "\n";
		$this->num++;
	}
	function key() {
		return $this->num;
	}
}
	
class c implements IteratorAggregate {

	public $max = 4;

	function getIterator() {
		echo __METHOD__ . "\n";
		return new c_iter($this);
	}
}

$t = new c();

foreach($t as $v) {
	if ($v == 0) {
		echo "continue outer\n";
		continue;
	}
	foreach($t as $w) {
		if ($w == 1) {
			echo "continue inner\n";
			continue;
		}
		if ($w == 2) {
			echo "break inner\n";
			break;
		}
		echo "double:$v:$w\n";
	}
	if ($v == 2) {
		echo "break outer\n";
		break;
	}
}

print "Done\n";
?>
--EXPECT--
c::getIterator
c_iter::__construct
c_iter::rewind
c_iter::hasMore = true
c_iter::current
c_iter::next
continue outer
c_iter::hasMore = true
c_iter::current
c_iter::next
c::getIterator
c_iter::__construct
c_iter::rewind
c_iter::hasMore = true
c_iter::current
c_iter::next
double:1:0
c_iter::hasMore = true
c_iter::current
c_iter::next
continue inner
c_iter::hasMore = true
c_iter::current
c_iter::next
break inner
c_iter::hasMore = true
c_iter::current
c_iter::next
c::getIterator
c_iter::__construct
c_iter::rewind
c_iter::hasMore = true
c_iter::current
c_iter::next
double:2:0
c_iter::hasMore = true
c_iter::current
c_iter::next
continue inner
c_iter::hasMore = true
c_iter::current
c_iter::next
break inner
break outer
Done
