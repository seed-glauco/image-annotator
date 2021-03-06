"use strict";

(function (EditorTemplates) {
  /*-----------------------------------------------------------------------------------------*/

  const toolbarTemplate = `
    <form class="vanilla-tagger-toolbar" onsubmit="return false;">

     <input type="radio" class="toggler" name="tagType" id="tagTypeDot" value="dot" checked>
     <label for="tagTypeDot">Dot</label>     
     <input type="radio" class="toggler" name="tagType" id="tagTypeHotspot" value="hotspot" disabled>
     <label for="tagTypeHotspot">Hotspot</label>

       

     <button id="btn-export">Export data</button>

    </form>
  `;

  /*-----------------------------------------------------------------------------------------*/

  const dialogFormTemplate = `
    <dialog class="vanilla-tagger-dialog" id="vt-dialogForm">
        <header></header>
        <main></main>
        <footer>
        </footer>
    </dialog>
    `;

  /*-----------------------------------------------------------------------------------------*/

  const dialogExportTemplate = `
    <dialog class="vanilla-tagger-dialog"  id="vt-dialogExport">
        <header>Export configuration data</header>
        <main><pre></pre></main>
        <footer>
          <button class="btn-close">OK</button>
        </footer>
    </dialog>
    `;

  /*-----------------------------------------------------------------------------------------*/

  const dialogHeaderTemplate = (tag) => `
    Tag #${tag.index} <button data-index="${tag.index}" class="btn-remove">Remove</button>
    `;

  /*-----------------------------------------------------------------------------------------*/

  const dialogFooterTemplate = (tag) => `
    <button class="btn-confirm" type="submit" data-index="${tag.index}">OK</button>
    <button class="btn-cancel">Cancel</button>
  `;

  /*-----------------------------------------------------------------------------------------*/
  /*---------------------------------- WEBCOMPONENT CLASS -----------------------------------*/
  /*-----------------------------------------------------------------------------------------*/

  class VanillaTaggerEditor extends VanillaTagger {
    static get observedAttributes() {
      return [
        "src",
        "placeholder",
        "data-tags",
        "data-theme",
        "data-theme-text",
      ];
    }

    /*--------------------------------- CLASS METHODS ---------------------------------------*/

    constructor() {
      super();

      let host = this;
      _ready(function () {
        _renderEditor(host);
      });
    }

    /*---------------------------------------------------------------------------------------*/

    get placeholder() {
      return this.getAttribute("placeholder");
    }

    /*---------------------------------------------------------------------------------------*/

    set placeholder(value) {
      this.setAttribute("placeholder", value);
    }

    /*---------------------------------------------------------------------------------------*/

    get publishedTags() {
      let host = this;
      return EditorTemplates.processTags(host);
    }

    /*---------------------------------------------------------------------------------------*/

    attributeChangedCallback(name, oldValue, newValue) {
      let host = this;

      if (
        oldValue === newValue ||
        (name !== "placeholder" &&
          (!this ||
            !this.context.wrapper ||
            this.classList.contains("updating")))
      )
        return false;

      if (name === "src") this.loadImage();
      else if (name === "data-tags") this.loadTags();
      else if (name === "data-theme" || name === "data-theme-text")
        this.applyStyles();
      else
        _ready(function () {
          _renderEditor(host);
        });
    }

    /*----------------------------------------------------------------------------------------*/
  }

  /*------------------------------------------------------------------------------------------*/
  /*------------------------------------- PRIVATE METHODS ------------------------------------*/
  /*------------------------------------------------------------------------------------------*/

  /*----------------------------------------------------------------------------------------*/

  const _ready = function _ready(fn) {
    if (
      document.attachEvent
        ? document.readyState === "complete"
        : document.readyState !== "loading"
    ) {
      fn();
    } else {
      document.addEventListener("DOMContentLoaded", fn);
    }
  };

  /*----------------------------------------------------------------------------------------*/

  const _renderEditor = async function _renderEditor(host) {
    if (!host.placeholder || host.classList.contains("editor-rendered"))
      return false;

    let container = document.querySelector(host.placeholder),
      toolbarTmpl = document.createElement("template"),
      dialogFormTmpl = document.createElement("template"),
      dialogExportTmpl = document.createElement("template"),
      toolbar,
      dialogForm,
      dialogExport;

    if (container.length < 1) return false;

    if (typeof HTMLDialogElement != "function") {
      await Promise.all([
        _fetchResource(
          "https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.5.1/dialog-polyfill.min.css",
          "css",
          "dialog-polyfill-css",
          host
        ),
        _fetchResource(
          "https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.5.1/dialog-polyfill.min.js",
          "js",
          "dialog-polyfill-js",
          host
        ),
      ]);
    }

    host.dataset.type = "dot"; //initial

    toolbarTmpl.innerHTML = toolbarTemplate;
    toolbar = toolbarTmpl.content.cloneNode(true);
    dialogFormTmpl.innerHTML = dialogFormTemplate;
    dialogForm = dialogFormTmpl.content.cloneNode(true);
    dialogExportTmpl.innerHTML = dialogExportTemplate;
    dialogExport = dialogExportTmpl.content.cloneNode(true);

    container.innerHTML = "";
    container.appendChild(toolbar);
    container.appendChild(dialogForm);
    container.appendChild(dialogExport);

    host.context.toolbar = container.querySelector(".vanilla-tagger-toolbar");
    host.context.dialogForm = container.querySelector("#vt-dialogForm");
    host.context.dialogExport = container.querySelector("#vt-dialogExport");

    if (window.dialogPolyfill) {
      window.dialogPolyfill.registerDialog(host.context.dialogForm);
      window.dialogPolyfill.registerDialog(host.context.dialogExport);
    }

    _attachEvents(host);

    host.classList.add("editor-rendered");
  };

  /*-----------------------------------------------------------------------------------------*/

  const _fetchResource = async function _fetchResource(url, type, id, host) {
    return new Promise((resolve, reject) => {
      let element, elementType, props;

      if (id) {
        const res = document.getElementById(id);
        if (res) return true;
      }

      if (type === "js") {
        elementType = "script";
        props = {
          id: id,
          type: "text/javascript",
          src: url,
        };
      } else if (type === "css") {
        elementType = "link";
        props = {
          id: id,
          type: "text/css",
          rel: "stylesheet",
          href: url,
        };
      }

      if (elementType) {
        let element = document.createElement(elementType);

        element.onload = () => {
          resolve(element);
        };
        element.onerror = () => {
          reject(element);
        };

        for (const property in props) {
          element[property] = props[property];
        }

        document.head.appendChild(element);
      }
    });
  };

  /*-----------------------------------------------------------------------------------------*/

  const _attachEvents = function _attachEvents(host) {
    let eventNames =
      "mousedown mouseup mousemove touchstart touchend touchmove click";

    eventNames.split(" ").forEach(function (eventName) {
      host.addEventListener(
        eventName,
        function (e) {
          _handleEvent(host, event);
        },
        false
      );
    });

    host.context.toolbar
      .querySelectorAll("[name='tagType']")
      .forEach(function (chk) {
        chk.addEventListener("change", function (event) {
          host.dataset.type = this.getAttribute("value");
        });
      });

    host.context.toolbar
      .querySelector("#btn-export")
      .addEventListener("click", function (event) {
        _openExportDialog(host);
      });

    host.addEventListener("VanillaTagger:tagClick", function (event) {
      event.preventDefault();
      event.stopPropagation();

      _openEditDialog(host, event.detail);
    });

    host.context.dialogForm.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const btn = event.target;

      if (btn.classList.contains("btn-remove"))
        _removeTag(host, btn.dataset.index);
      else if (btn.classList.contains("btn-confirm"))
        _updateTag(host, btn.dataset.index);
      else if (btn.classList.contains("btn-cancel"))
        host.context.dialogForm.close();
    });

    host.context.dialogExport.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const btn = event.target;

      if (btn.classList.contains("btn-close"))
        host.context.dialogExport.close();
    });
  };

  /*-----------------------------------------------------------------------------------------*/

  const _handleEvent = function _handleEvent(host, event) {
    event.preventDefault();
    event.stopPropagation();

    let type = host.dataset.type;

    if (type === "dot" && event.type === "click") {
      let tag = _addDot(host, event.offsetX, event.offsetY);
      _openEditDialog(host, tag);
    } else if (type === "dot" && event.type === "touchstart") {
      let tag = _addDot(
        host,
        event.touches[0].pageX - event.touches[0].target.offsetLeft,
        event.touches[0].pageY - event.touches[0].target.offsetTop
      );
      _openEditDialog(host, tag);
    }
  };

  /*-----------------------------------------------------------------------------------------*/

  const _addDot = function _addDot(host, x, y) {
    let tags = host.tags,
      dot = {},
      coords = _percentValues(host, x, y);

    dot.classes =
      "tag dot blank" +
      (EditorTemplates && EditorTemplates.tagDefaultClasses
        ? " " + EditorTemplates.tagDefaultClasses
        : "");
    dot.top = coords.y;
    dot.left = coords.x;

    tags.push(dot);

    host.tags = tags;

    return host.getTagByIndex(host.tags.length);
  };

  /*-----------------------------------------------------------------------------------------*/

  const _percentValues = function _percentValues(host, x, y) {
    let hostW = host.clientWidth;
    let hostH = host.clientHeight;

    let xperc = (100 * x) / hostW;
    let yperc = (100 * y) / hostH;

    return { x: xperc, y: yperc };
  };

  /*-----------------------------------------------------------------------------------------*/

  const _openEditDialog = function _openEditDialog(host, tag) {
    const dialog = host.context.dialogForm;

    dialog.querySelector("header").innerHTML = dialogHeaderTemplate(tag);
    dialog.querySelector("footer").innerHTML = dialogFooterTemplate(tag);

    if (EditorTemplates && EditorTemplates.tagForm) {
      let form = document.createElement("form");
      form.classList.add("vanilla-tagger-tagdata");
      form.onsubmit = function (evt) {
        evt.preventDefault();
        return false;
      };
      form.innerHTML = EditorTemplates.tagForm(tag);

      let main = dialog.querySelector("main");
      main.innerHTML = "";
      main.appendChild(form);
    }

    dialog.showModal();
  };

  /*-----------------------------------------------------------------------------------------*/

  const _openExportDialog = function _openExportDialog(host) {
    const dialog = host.context.dialogExport,
      pre = document.createElement("pre"),
      exportData = {};

    exportData.src = host.src;
    exportData.placeholder = host.placeholder;

    if (EditorTemplates && EditorTemplates.processTags) {
      exportData.tags = EditorTemplates.processTags(host);
    } else {
      exportData.tags = host.tags;
    }

    dialog.querySelector("pre").textContent = JSON.stringify(
      exportData,
      undefined,
      2
    );

    dialog.showModal();
  };

  /*-----------------------------------------------------------------------------------------*/

  const _removeTag = function _removeTag(host, index) {
    if (confirm("Are you sure?")) {
      let tags = host.tags;
      tags.splice(index - 1, 1);
      host.tags = tags;

      host.context.dialogForm.close();
    }
  };

  /*-----------------------------------------------------------------------------------------*/

  const _updateTag = function _updateTag(host, index) {
    let form = host.context.dialogForm.querySelector("form");

    if (!form.checkValidity()) {
      return false;
    }

    if (EditorTemplates && EditorTemplates.buildTag) {
      let tags = host.tags;

      tags[index - 1] = EditorTemplates.buildTag(tags[index - 1], form);

      host.tags = tags;
    }

    host.context.dialogForm.close();
  };

  /*-----------------------------------------------------------------------------------------*/

  if (!window.VanillaTaggerEditor) {
    window.VanillaTaggerEditor = VanillaTaggerEditor;

    customElements.define("vanilla-tagger-editor", VanillaTaggerEditor);
  }

  /*-----------------------------------------------------------------------------------------*/
  /*-----------------------------------------------------------------------------------------*/
})(VanillaTaggerEditorTemplates);
