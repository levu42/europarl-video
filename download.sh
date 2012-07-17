#!/bin/bash
if [ "$1" = "-f" ]
then
	file=$2
	isfile="y"
else
	url=$1
	isfile="n"
fi

the_file() {
	if [ $isfile = "y" ]
	then
		cat $file
	else
		wget $url -o /dev/null -O - 2>/dev/null
	fi
}
the_js() {
	the_file | grep hmap | sed -e 's/\s\+var\s\+\(.*\)\s\+=\s\+new\s\+Array()/data.\1 = []/g' | sed -e 's/\s\+hmap/data.hmap/g'
}
the_json() {
	tmpf=`mktemp --tmpdir=.`
	(cat header.js; the_js; cat footer.js) > $tmpf
	node $tmpf;
	rm $tmpf;
}
the_json | ./hmap2apiresult.php
