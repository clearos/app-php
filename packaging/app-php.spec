
Name: app-php
Epoch: 1
Version: 2.0.5
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
Requires: app-date-core >= 1:1.6.2
Requires: app-web-server-core
Requires: php >= 5.3.3
Requires: php-gd >= 5.3.3
Requires: php-ldap >= 5.3.3
Requires: php-mbstring >= 5.3.3
Requires: php-mysql >= 5.3.3
Requires: php-process >= 5.3.3
Requires: php-soap >= 5.3.3
Requires: php-xml >= 5.3.3

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
install -D -m 0755 packaging/date-event %{buildroot}/var/clearos/events/date/php
install -D -m 0644 packaging/php.conf %{buildroot}/etc/clearos/php.conf

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
%dir /usr/clearos/apps/php
%dir /var/clearos/php
/usr/clearos/apps/php/deploy
/usr/clearos/apps/php/language
/var/clearos/events/date/php
%config(noreplace) /etc/clearos/php.conf
