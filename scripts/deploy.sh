#!/usr/bin/env bash

# 1. Clone complete SVN repository to separate directory
svn co $WP_REPOSITORY ../svn --username $WP_USERNAME --password $WP_PASSWORD --non-interactive

# 2. Copy git repository contents to SVN trunk/ directory
cp -R ./* ../svn/trunk/

# 3. Switch to SVN repository
cd ../svn/trunk/

# 4. Move assets/ to SVN /assets/
mv ./assets/ ../assets/

# 5. Clean up unnecessary files
rm -rf .git/
rm -rf scripts/
rm -rf tests/
rm README.md
rm phpunit.xml
rm phpcs.ruleset.xml
# rm .travis.yml

# add new files in trunk
svn add --force .

# 6. Go to SVN repository root
cd ../

# 7. Create SVN tag
svn cp trunk tags/$TRAVIS_TAG

# 8. Push SVN tag
svn ci  --message "Release $TRAVIS_TAG"  --username $WP_USERNAME --password $WP_PASSWORD --non-interactive