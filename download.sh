#!/bin/bash
url=$1
the_js() {
	wget $url -o /dev/null -O - 2>/dev/null | grep hmap | sed -e 's/\s\+var\s\+\(.*\)\s\+=\s\+new\s\+Array()/data.\1 = []/g' | sed -e 's/\s\+hmap/data.hmap/g'
}
the_json() {
	tmpf=`mktemp --tmpdir=.`
	(cat header.js; the_js; cat footer.js) > $tmpf
	node $tmpf;
	rm $tmpf;
}
the_json | ./hmap2apiresult.php
