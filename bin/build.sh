#!/usr/bin/env bash

# Move to plug-in root
if [[ `pwd` == */bin ]]
then
	pushd ../ > /dev/null
	LVDBID_RESTORE_DIR=true
else
	LVDBID_RESTORE_DIR=false
fi

# Store some stuff for later use
LVDBID_CDIR=$(pwd)

LVDBID_BUILD_ROOTDIR="$LVDBID_CDIR/build"
LVDBID_BUILD_OUTDIR="$LVDBID_BUILD_ROOTDIR/output"
LVDBID_BUILD_TMPDIR="$LVDBID_BUILD_ROOTDIR/tmp"

LVDBID_VERSION=$(awk '{IGNORECASE=1}/Version:/{print $NF}' ./lvd-bloginfo-display-plugin-main.php | awk '{gsub(/\s+/,""); print $0}')
LVDBID_BUILD_NAME="lvd-bloginfo-display.$LVDBID_VERSION.zip"

# Ensure all output directories exist
ensure_out_dirs() {
	echo "Ensuring output directory structure..."

	if [ ! -d $LVDBID_BUILD_ROOTDIR ]
	then
		mkdir $LVDBID_BUILD_ROOTDIR
	fi

	if [ ! -d $LVDBID_BUILD_OUTDIR ] 
	then
		mkdir $LVDBID_BUILD_OUTDIR
	fi

	if [ ! -d $LVDBID_BUILD_TMPDIR ] 
	then
		mkdir $LVDBID_BUILD_TMPDIR
	fi
}

clean_tmp_dir() {
	echo "Cleaning up temporary directory..."
	rm -rf $LVDBID_BUILD_TMPDIR/*
	rm -rf $LVDBID_BUILD_TMPDIR/.htaccess
}

# Clean output directories
clean_out_dirs() {
	echo "Ensuring output directories are clean..."
	rm -rf $LVDBID_BUILD_OUTDIR/* > /dev/null
	rm -rf $LVDBID_BUILD_TMPDIR/* > /dev/null
}

# Copy over all files
copy_source_files() {
	echo "Copying all files..."
	cp ./LICENSE "$LVDBID_BUILD_TMPDIR/license.txt"
	cp ./index.php "$LVDBID_BUILD_TMPDIR"
	cp ./lvd-bloginfo-display-plugin-main.php "$LVDBID_BUILD_TMPDIR"

	mkdir "$LVDBID_BUILD_TMPDIR/media" && cp -r ./media/* "$LVDBID_BUILD_TMPDIR/media"
	mkdir "$LVDBID_BUILD_TMPDIR/views" && cp -r ./views/* "$LVDBID_BUILD_TMPDIR/views"
	mkdir "$LVDBID_BUILD_TMPDIR/lib" && cp -r ./lib/* "$LVDBID_BUILD_TMPDIR/lib"
}

generate_package() {
	echo "Generating archive..."
	pushd $LVDBID_BUILD_TMPDIR > /dev/null
	zip -rT $LVDBID_BUILD_OUTDIR/$LVDBID_BUILD_NAME ./ > /dev/null
	popd > /dev/null
}

echo "Using version: ${LVDBID_VERSION}"

ensure_out_dirs
clean_out_dirs
copy_source_files
generate_package
clean_tmp_dir

echo "DONE!"

if [ "$LVDBID_RESTORE_DIR" = true ]
then
	popd > /dev/null
fi