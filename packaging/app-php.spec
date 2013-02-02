
Name: app-php
Epoch: 1
Version: 1.4.15
Release: 1%{dist}
Summary: PHP - Core
License: LGPLv3
Group: ClearOS/Libraries
Source: app-php-%{version}.tar.gz
Buildarch: noarch

%description
The PHP app provides management tools for the underlying PHP web server technology.

%package core
Summary: PHP - Core
Requires: app-base-core
Requires: app-web-server-core
Requires: php >= 5.3.3
Requires: php-imap >= 5.3.3
Requires: php-mysql >= 5.3.3
Requires: php-mbstring >= 5.3.3

%description core
The PHP app provides management tools for the underlying PHP web server technology.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/php
cp -r * %{buildroot}/usr/clearos/apps/php/

install -d -m 0755 %{buildroot}/var/clearos/php

%post core
logger -p local6.notice -t installer 'app-php-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/php/deploy/install ] && /usr/clearos/apps/php/deploy/install
fi

[ -x /usr/clearos/apps/php/deploy/upgrade ] && /usr/clearos/apps/php/deploy/upgrade

exit 0

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-php-core - uninstalling'
    [ -x /usr/clearos/apps/php/deploy/uninstall ] && /usr/clearos/apps/php/deploy/uninstall
fi

exit 0

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/php/packaging
%exclude /usr/clearos/apps/php/tests
%dir /usr/clearos/apps/php
%dir /var/clearos/php
/usr/clearos/apps/php/deploy
/usr/clearos/apps/php/language
/usr/clearos/apps/php/libraries
