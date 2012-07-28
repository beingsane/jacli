#!/usr/bin/python -d
import sys, os

from PyQt4 import QtCore, QtGui

from jacli_ui import Ui_MainWindow
#from run import app
#global app
class JACLI(QtGui.QMainWindow):
    base_dir = ''# = "/home/jtester/repos/jacli/config"

    def __init__(self, parent=None):
        QtGui.QWidget.__init__(self, parent)
        self.base_dir = '/home/jtester/repos/jacli/config'
        self.ui = Ui_MainWindow()
        self.ui.setupUi(self)
        self.ui.list_apps.show()
        for dirname, dirnames, filenames in os.walk(self.base_dir+'/repositories'):
            # Add folder to list
            #print oFiles
            for filename in filenames:
                print os.path.join(dirname, filename)
                print filename
         #   aDirs.append( oDirPaths )

                i = QtGui.QListWidgetItem(self.ui.list_apps)
                i.text = filename
                i.statusTip = os.path.join(dirname, filename)

                print i.text

        dirList = os.listdir(self.base_dir+'/repositories')

        for fname in dirList:
            print fname
            i = QtGui.QListWidgetItem(self.ui.list_apps)
            i.text = fname
            i.statusTip = os.path.join(self.base_dir+'/repositories', fname)


        self.ui.list_apps.show()
        self.ui.list_apps.update()
        self.ui.retranslateUi(self.ui)

     ##   QtCore.QObject.connect(self.ui.list_apps, QtCore.SIGNAL("itemClicked(QListWidgetItem)"),
    #        self.fill_versions)

        QtCore.QObject.connect(self.ui.list_apps, QtCore.SIGNAL("clicked()"),
            self.close)

        QtCore.QObject.connect(self.ui.button_deploy, QtCore.SIGNAL("clicked()"), self.deploy)
        #QtCore.QObject.connect(self.ui.button_deploy, QtCore.SIGNAL("clicked()"), self, QtCore.SLOT("deploy()"))
        #    QtCore.QObject.connect(self.ui.lineEdit, QtCore.SIGNAL("returnPressed()"), self.add_entry)
        #QtCore.QObject.connect(self.ui.button_quit, QtCore.SIGNAL("clicked()"), self, QtCore.SLOT("close()"))
        QtCore.QObject.connect(self.ui.button_quit, QtCore.SIGNAL("clicked()"), self.close)


    def deploy(self):
        print 'huhu'
        print self.base_dir

    def fill_versions(self, aaa):
        self.ui.list_versions.clear()

        #doc = REXML::Document.new File.read app_name.statusTip

        #doc.elements.each('repository/versions/version') do |p|
        #i = Qt::ListWidgetItem.new(@ui.list_versions)
        #i.text = p.elements['version'].text
        #end

        #self.read_application_config app_name

        #self.ui
        print 'ha'+aaa


    def read_common_config(self):
        #   self.ui.lineEdit.selectAll()
        #  self.ui.lineEdit.cut()
        # self.ui.textEdit.append("")
        #self.ui.textEdit.paste()
        self.ui

