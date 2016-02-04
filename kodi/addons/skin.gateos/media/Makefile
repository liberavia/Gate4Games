TARGET=Textures.xbt

IMAGES := $(shell find . -name "*jpg")
IMAGES += $(shell find . -name "*png")
IMAGES += $(shell find . -name "*gif")

all: $(TARGET)

.PHONY: force

$(TARGET): $(IMAGES)
ifeq (i486-linux,arm)
	/build/kodi-dRTkJl/kodi-15.1~git20150816.1137-final/tools/depends/native/TexturePacker/bin/TexturePacker -use_none -input . -output $(TARGET)
else
	/build/kodi-dRTkJl/kodi-15.1~git20150816.1137-final/tools/depends/native/TexturePacker/bin/TexturePacker -input . -output $(TARGET)

endif

clean:
	rm -f $(TARGET)
distclean: clean

