#!/usr/bin/env ruby

require 'Qt4'
require 'jacli'

$a = Qt::Application.new(ARGV)
jacli = Jacli.new
jacli.show
$a.exec

#about = KDE::AboutData.new("mainwindow", "MainWindow", KDE.ki18n(""), "0.1")
#KDE::CmdLineArgs.init(ARGV, about)

#a = KDE::Application.new

#u = Jacli::MainWindow.new
#w = Qt::MainWindow.new

#u.setup_ui(w)

#a.topWidget = w

#w.show

#a.exec
