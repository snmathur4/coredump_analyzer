#!/bin/bash

ROOT_DIR=$(cd `dirname $0` && pwd)
cd $ROOT_DIR

FOLDERNUM=$(ls | sed 's/enum-//' | sort -n | tail -1)
COREDUMP_FOLDER=$ROOT_DIR"/"$FOLDERNUM
CORE_NAME=$1
RFS_NAME=$2

# Copy .gdbinit over
cp .gdbinit $FOLDERNUM/.gdbinit

# cd to coredump folder
cd $COREDUMP_FOLDER
echo $PWD

# unzip the coredump and untar the symbols
gzip -d $CORE_NAME
tar xvf $RFS_NAME

# Run GDB and pass in the bnserver binary
/home/u-pc/gdb-7.7/gdb/gdb -batch -ex "set logging on" -ex "bt" -ex "thread apply all bt full" usr/lib/DeltaControls/BACnetServer5/bnserver eDelta.dp -ex "set logging off"

cp gdb.txt $ROOT_DIR

cd $ROOT_DIR
#rm -R $COREDUMP_FOLDER