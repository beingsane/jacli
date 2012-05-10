import sys
from PyQt4 import QtGui
from jacli import JACLI

app = QtGui.QApplication(sys.argv)
jacli = JACLI()
jacli.show()
sys.exit(app.exec_())