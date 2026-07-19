/**
 * TickTweezers Quote Configurator — front-end controller.
 * Vanilla JS, no framework/build step. Relies on TTQ_DATA (localized
 * from PHP) for ajaxUrl / nonce / settings / i18n.
 *
 * Steps:
 *  Panel 1 → "Step 1 of 3" — Choose Product
 *  Panel 2 → "Step 2 of 3" — Customize Specifications
 *  Panel 3 → "Step 3 of 3" — Contact Details
 *  Panel 4 → "Step 3 of 3" (review, same sidebar number) — Review
 */
(function () {
  "use strict";

  if (typeof TTQ_DATA === "undefined") {
    return;
  }

  var STORAGE_KEY = "ttq_quote_state_v2";

  var wizard = document.getElementById("ttq-wizard");
  if (!wizard) {
    return;
  }

  var form = document.getElementById("ttq-form");
  var railDots = wizard.querySelectorAll(".ttq-rail__dot");
  var railCurrent = wizard.querySelector(".ttq-js-rail-current");
  var railOf = wizard.querySelector(".ttq-js-rail-of");
  var panels = wizard.querySelectorAll(".ttq-panel");
  var overlay = wizard.querySelector(".ttq-js-overlay");
  var topbarEyebrow = wizard.querySelector(".ttq-js-step-eyebrow");
  var topbarHeading = wizard.querySelector(".ttq-js-step-heading");
  var trustBadge = wizard.querySelector(".ttq-js-trust-badge");
  var completedBadge = wizard.querySelector(".ttq-js-completed-badge");
  var trackItems = wizard.querySelectorAll(".ttq-step-track__item");
  var trackLines = wizard.querySelectorAll(".ttq-step-track__line");

  var currentStep = 1;
  var totalPanels = panels.length; // 4

  /* ── Step metadata ──────────────────────────────────────────── */
  // Sidebar dots represent steps 1–3 (panel 4 = review = still "step 3")
  var STEP_META = [
    null, // 1-indexed
    {
      eyebrow: "Step 1 of 4",
      heading: "Start by Choosing Your Product",
      railNum: 1,
      trackActive: 1,
    },
    {
      eyebrow: "Step 2 of 4",
      heading: "Customize Your Tick Tweezers",
      railNum: 2,
      trackActive: 2,
    },
    {
      eyebrow: "Step 3 of 4",
      heading: "Contact Details",
      railNum: 3,
      trackActive: 3,
    },
    {
      eyebrow: "Step 4 of 4",
      heading: "Review & Submit",
      railNum: 4,
      trackActive: 4,
    },
  ];

  /* ── State ──────────────────────────────────────────────────── */
  var state = {
    product: "",
    quantity: "",
    colors: [],
    sizes: [],
    side1: "",
    side2: "",
    organization: "",
    name: "",
    phone: "",
    email: "",
    free_sample: "no",
    address: "",
    logoToken: "",
    logoPreviewUrl: "",
    logoFileName: "",
    custom_color: "",
    material: "fabric",
    carabiner_clip: "no",
    comments: "",
  };

  var PRODUCT_LABELS = {
    tweezers_only: "Tick Tweezers Only",
    complete_kit: "Complete Tick Kit",
  };

  /* ── Session persistence ────────────────────────────────────── */
  function saveState() {
    try {
      sessionStorage.setItem(
        STORAGE_KEY,
        JSON.stringify({ state: state, step: currentStep }),
      );
    } catch (e) {}
  }

  function loadState() {
    try {
      var raw = sessionStorage.getItem(STORAGE_KEY);
      if (!raw) {
        return;
      }
      var parsed = JSON.parse(raw);
      if (parsed && parsed.state) {
        state = Object.assign(state, parsed.state);
        currentStep = parsed.step || 1;
      }
    } catch (e) {}
  }

  function applyStateToForm() {
    var productInput = form.querySelector(
      'input[name="product"][value="' + cssEscape(state.product) + '"]',
    );
    if (productInput) {
      productInput.checked = true;
    }

    setVal("quantity", state.quantity);

    state.colors.forEach(function (c) {
      var el = form.querySelector(
        'input[name="colors[]"][value="' + cssEscape(c) + '"]',
      );
      if (el) {
        el.checked = true;
      }
    });
    state.sizes.forEach(function (s) {
      var el = form.querySelector(
        'input[name="sizes[]"][value="' + cssEscape(s) + '"]',
      );
      if (el) {
        el.checked = true;
      }
    });

    setVal("side1", state.side1);
    setVal("side2", state.side2);
    setVal("custom_color", state.custom_color);
    updateCharCounters();

    setVal("organization", state.organization);
    setVal("name", state.name);
    setVal("phone", state.phone);
    setVal("email", state.email);
    setVal("address", state.address);

    // Free sample radio
    if (state.free_sample) {
      var fsEl = form.querySelector(
        'input[name="free_sample"][value="' + state.free_sample + '"]',
      );
      if (fsEl) {
        fsEl.checked = true;
      }
    }

    if (state.logoToken) {
      showUploadPreview(state.logoPreviewUrl, state.logoFileName);
    }

    var materialEl = form.querySelector(
      'input[name="material"][value="' + cssEscape(state.material) + '"]',
    );
    if (materialEl) {
      materialEl.checked = true;
    }
    var clipEl = form.querySelector(
      'input[name="carabiner_clip"][value="' +
        cssEscape(state.carabiner_clip) +
        '"]',
    );
    if (clipEl) {
      clipEl.checked = true;
    }
    setVal("comments", state.comments);

    filterColorsAndSizes();
    updateProductUI();
    updateFreeSampleUI();
  }

  /* ── Product-dependent UI: banner, headings, product-only sections ── */
  function updateProductUI() {
    var product = productFor(state.product);
    var label = product
      ? product.label
      : PRODUCT_LABELS[state.product] || state.product;

    // Selection confirmation banner (Step 1)
    var banner = wizard.querySelector(".ttq-js-selected-banner");
    var bannerName = wizard.querySelector(".ttq-js-selected-banner-name");
    if (banner) {
      if (state.product) {
        banner.classList.add("is-visible");
        if (bannerName) {
          bannerName.textContent = label;
        }
      } else {
        banner.classList.remove("is-visible");
      }
    }

    var isKit = state.product === "complete_kit";

    // Step 2 heading
    var step2Title = wizard.querySelector(".ttq-js-step2-title");
    if (step2Title) {
      step2Title.textContent = isKit
        ? "Customize Your Complete Tick Kit"
        : "Customize Your Tick Tweezers";
    }

    // Quantity hint text
    var qtyHint = wizard.querySelector(".ttq-js-qty-hint");
    if (qtyHint) {
      qtyHint.textContent = isKit
        ? "Enter the total number of tick kits you need."
        : "Enter the total number of customized tick tweezers you need.";
    }

    // Side 1 / Side 2 -> Front / Back labels for the kit
    var side1Label = wizard.querySelector(".ttq-js-side1-label");
    var side2Label = wizard.querySelector(".ttq-js-side2-label");
    if (side1Label) {
      side1Label.firstChild.textContent = isKit
        ? "Front Stamping"
        : "Side 1 Stamping";
    }
    if (side2Label) {
      side2Label.firstChild.textContent = isKit
        ? "Back Stamping "
        : "Side 2 Stamping ";
    }

    // Product-only sections (Material / Carabiner Clip — Complete Kit only)
    wizard.querySelectorAll(".ttq-js-product-only").forEach(function (el) {
      var only = el.getAttribute("data-product-only");
      el.style.display = !only || only === state.product ? "" : "none";
    });
  }

  /* ── Free sample toggle: show/hide shipping address ─────────────── */
  function updateFreeSampleUI() {
    var wantsSample = state.free_sample === "yes";
    var addressSection = wizard.querySelector(".ttq-js-address-section");
    var sampleNote = wizard.querySelector(".ttq-js-sample-note");
    var addressField = form.querySelector('[name="address"]');

    if (addressSection) {
      addressSection.style.display = wantsSample ? "" : "none";
    }
    if (sampleNote) {
      sampleNote.hidden = !wantsSample;
    }
    if (addressField) {
      addressField.required = wantsSample;
    }
  }

  wizard.addEventListener("change", function (e) {
    if (e.target.classList && e.target.classList.contains("ttq-js-product-radio")) {
      readFormIntoState();
      updateProductUI();
    }
    if (e.target.classList && e.target.classList.contains("ttq-js-free-sample")) {
      readFormIntoState();
      updateFreeSampleUI();
    }
  });

  function filterColorsAndSizes() {
    var selectedProductKey = state.product;
    var product = productFor(selectedProductKey);
    if (!product) {
      return;
    }

    // Available color keys and size keys for this product (stored as comma strings)
    var availableColors = (product.colors || "")
      .split(",")
      .map(function (s) {
        return s.trim();
      })
      .filter(Boolean);
    var availableSizes = (product.sizes || "")
      .split(",")
      .map(function (s) {
        return s.trim();
      })
      .filter(Boolean);

    // If colors or sizes are empty, show all. Otherwise show only selected ones.
    var colorSwatches = wizard.querySelectorAll(".ttq-color-swatch");
    colorSwatches.forEach(function (swatch) {
      var input = swatch.querySelector('input[name="colors[]"]');
      if (!input) {
        return;
      }
      var key = input.value;
      var isAvailable =
        availableColors.length === 0 || availableColors.indexOf(key) !== -1;

      if (isAvailable) {
        swatch.style.display = "";
      } else {
        swatch.style.display = "none";
        input.checked = false; // Uncheck hidden inputs
      }
    });

    // Sizes are opt-in per product: if the product has no sizes configured
    // at all, hide the whole "Select Size(s)" section instead of showing
    // every globally-defined size.
    var sizesSection = wizard.querySelector(".ttq-js-sizes-section");
    var sizeChips = wizard.querySelectorAll(".ttq-size-chip");

    if (availableSizes.length === 0) {
      if (sizesSection) {
        sizesSection.style.display = "none";
      }
      sizeChips.forEach(function (chip) {
        var input = chip.querySelector('input[name="sizes[]"]');
        chip.style.display = "none";
        if (input) {
          input.checked = false;
        }
      });
    } else {
      if (sizesSection) {
        sizesSection.style.display = "";
      }
      sizeChips.forEach(function (chip) {
        var input = chip.querySelector('input[name="sizes[]"]');
        if (!input) {
          return;
        }
        var key = input.value;
        var isAvailable = availableSizes.indexOf(key) !== -1;

        if (isAvailable) {
          chip.style.display = "";
        } else {
          chip.style.display = "none";
          input.checked = false; // Uncheck hidden inputs
        }
      });
    }
  }

  function setVal(name, value) {
    var el = form.querySelector('[name="' + name + '"]');
    if (el && value !== undefined && value !== null) {
      el.value = value;
    }
  }

  function cssEscape(str) {
    return String(str).replace(/["\\]/g, "\\$&");
  }

  function readFormIntoState() {
    state.product =
      (form.querySelector('input[name="product"]:checked') || {}).value || "";
    var qtyRaw = (form.querySelector('[name="quantity"]') || {}).value;
    state.quantity = qtyRaw === "" || qtyRaw === undefined ? "" : (parseInt(qtyRaw, 10) || 0);
    state.colors = Array.prototype.map.call(
      form.querySelectorAll('input[name="colors[]"]:checked'),
      function (el) {
        return el.value;
      },
    );
    state.sizes = Array.prototype.map.call(
      form.querySelectorAll('input[name="sizes[]"]:checked'),
      function (el) {
        return el.value;
      },
    );
    state.side1 = (form.querySelector('[name="side1"]') || {}).value || "";
    state.side2 = (form.querySelector('[name="side2"]') || {}).value || "";
    state.organization =
      (form.querySelector('[name="organization"]') || {}).value || "";
    state.name = (form.querySelector('[name="name"]') || {}).value || "";
    state.phone = (form.querySelector('[name="phone"]') || {}).value || "";
    state.email = (form.querySelector('[name="email"]') || {}).value || "";
    state.address = (form.querySelector('[name="address"]') || {}).value || "";
    var freeEl = form.querySelector('input[name="free_sample"]:checked');
    state.free_sample = freeEl ? freeEl.value : "no";
    state.custom_color = (form.querySelector('[name="custom_color"]') || {}).value || "";

    var materialEl = form.querySelector('input[name="material"]:checked');
    state.material = materialEl ? materialEl.value : "fabric";
    var clipEl = form.querySelector('input[name="carabiner_clip"]:checked');
    state.carabiner_clip = clipEl ? clipEl.value : "no";
    state.comments = (form.querySelector('[name="comments"]') || {}).value || "";
  }

  /* ── Navigation ─────────────────────────────────────────────── */
  function goToStep(n) {
    currentStep = Math.min(Math.max(n, 1), totalPanels);
    wizard.setAttribute("data-step", currentStep);

    // Show/hide panels
    panels.forEach(function (panel) {
      var idx = parseInt(panel.getAttribute("data-panel"), 10);
      panel.hidden = idx !== currentStep;
    });

    var meta = STEP_META[currentStep];
    if (!meta) {
      return;
    }

    // Sidebar step number
    if (railCurrent) {
      railCurrent.textContent = meta.railNum;
    }

    // Sidebar dots — 4 dots representing steps 1-4
    railDots.forEach(function (dot) {
      var idx = parseInt(dot.getAttribute("data-step-index"), 10);
      dot.classList.remove("is-current", "is-complete", "is-disabled");
      if (idx < currentStep) {
        dot.classList.add("is-complete");
      } else if (idx === currentStep) {
        dot.classList.add("is-current");
      } else {
        dot.classList.add("is-disabled");
      }
    });

    // Top bar eyebrow + heading
    if (topbarEyebrow) {
      topbarEyebrow.textContent = meta.eyebrow;
    }
    if (topbarHeading) {
      topbarHeading.textContent = meta.heading;
    }

    // Step progress track — pass currentStep directly (1-4)
    updateTrack(currentStep);

    // 100% Completed badge + trust badge toggle on review
    if (currentStep === 4) {
      if (trustBadge) {
        trustBadge.hidden = true;
      }
      if (completedBadge) {
        completedBadge.hidden = false;
      }
    } else {
      if (trustBadge) {
        trustBadge.hidden = false;
      }
      if (completedBadge) {
        completedBadge.hidden = true;
      }
    }

    // Populate review panel
    if (currentStep === 4) {
      populateReview();
    }
    if (currentStep === 2) {
      filterColorsAndSizes();
      updateProductUI();
    }
    if (currentStep === 3) {
      updateFreeSampleUI();
    }

    // Focus heading for accessibility
    if (topbarHeading) {
      topbarHeading.setAttribute("tabindex", "-1");
      topbarHeading.focus();
    }

    // Scroll to top of wizard
    wizard.scrollIntoView({ behavior: "smooth", block: "start" });

    saveState();
  }

  function updateTrack(activeTrackStep) {
    // activeTrackStep == 5 means all steps done (submitted)
    var allDone = activeTrackStep >= 5;
    trackItems.forEach(function (item) {
      var ts = parseInt(item.getAttribute("data-track-step"), 10);
      item.classList.remove("is-active", "is-complete");
      if (allDone || ts < activeTrackStep) {
        item.classList.add("is-complete");
      } else if (ts === activeTrackStep) {
        item.classList.add("is-active");
      }
    });
    trackLines.forEach(function (line, i) {
      // i=0 connects step1→step2, i=1 connects step2→step3, i=2 connects step3→step4
      line.classList.toggle("is-complete", allDone || i + 1 < activeTrackStep);
    });
  }

  // Allow clicking completed rail dots to jump back
  railDots.forEach(function (dot) {
    dot.addEventListener("click", function () {
      if (dot.classList.contains("is-disabled")) {
        return;
      }
      readFormIntoState();
      var idx = parseInt(dot.getAttribute("data-step-index"), 10);
      goToStep(idx);
    });
  });

  /* ── Error helpers ──────────────────────────────────────────── */
  function clearErrors(panel) {
    panel.querySelectorAll(".ttq-field-error").forEach(function (el) {
      el.textContent = "";
    });
    panel.querySelectorAll(".ttq-invalid").forEach(function (el) {
      el.classList.remove("ttq-invalid");
    });
  }

  function showErrors(panel, errors) {
    Object.keys(errors).forEach(function (field) {
      var errEl = panel.querySelector('[data-error-for="' + field + '"]');
      if (errEl) {
        errEl.textContent = errors[field];
      }
      var input = panel.querySelector(
        '[name="' + field + '"], [name="' + field + '[]"]',
      );
      if (input) {
        input.classList.add("ttq-invalid");
      }
    });
  }

  /* ── AJAX step validation ───────────────────────────────────── */
  function ajaxValidateStep(stepKey, fields, panel, onValid) {
    var body = new FormData();
    body.append("action", "ttq_validate_step");
    body.append("nonce", TTQ_DATA.nonce);
    body.append("step", stepKey);
    Object.keys(fields).forEach(function (key) {
      var val = fields[key];
      if (Array.isArray(val)) {
        val.forEach(function (v) {
          body.append("fields[" + key + "][]", v);
        });
      } else {
        body.append("fields[" + key + "]", val);
      }
    });

    fetch(TTQ_DATA.ajaxUrl, {
      method: "POST",
      body: body,
      credentials: "same-origin",
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        clearErrors(panel);
        if (res.success) {
          onValid();
        } else if (res.data && res.data.errors) {
          showErrors(panel, res.data.errors);
          // Shake invalid fields to draw attention
          panel.querySelectorAll(".ttq-invalid").forEach(function (el) {
            el.classList.add("ttq-shake");
            setTimeout(function () {
              el.classList.remove("ttq-shake");
            }, 600);
          });
        } else {
          showErrors(panel, { submit: TTQ_DATA.i18n.genericError });
        }
      })
      .catch(function () {
        showErrors(panel, { submit: TTQ_DATA.i18n.genericError });
      });
  }

  function currentPanel() {
    return wizard.querySelector('.ttq-panel[data-panel="' + currentStep + '"]');
  }

  /* ── Next / Back ────────────────────────────────────────────── */
  wizard.addEventListener("click", function (e) {
    var nextBtn = e.target.closest(".ttq-js-next");
    var backBtn = e.target.closest(".ttq-js-back");

    if (nextBtn) {
      readFormIntoState();
      var panel = currentPanel();

      if (currentStep === 1) {
        ajaxValidateStep(
          "step-1",
          { product: state.product },
          panel,
          function () {
            goToStep(2);
          },
        );
      } else if (currentStep === 2) {
        ajaxValidateStep(
          "step-2",
          {
            product: state.product,
            quantity: state.quantity,
            colors: state.colors,
            sizes: state.sizes,
            side1: state.side1,
            side2: state.side2,
            custom_color: state.custom_color,
            material: state.product === "complete_kit" ? state.material : "",
            carabiner_clip:
              state.product === "complete_kit" ? state.carabiner_clip : "",
          },
          panel,
          function () {
            goToStep(3);
          },
        );
      } else if (currentStep === 3) {
        ajaxValidateStep(
          "step-3",
          {
            organization: state.organization,
            name: state.name,
            phone: state.phone,
            email: state.email,
            address: state.address,
            free_sample: state.free_sample,
          },
          panel,
          function () {
            goToStep(4);
          },
        );
      }
    }

    if (backBtn) {
      readFormIntoState();
      goToStep(currentStep - 1);
    }
  });

  /* ── Step 1: "What's Included" / "See Kit Contents" popup ─────── */
  var detailsModal = wizard.querySelector(".ttq-js-details-modal");

  function openDetailsModal(productKey) {
    if (!detailsModal) return;
    detailsModal.querySelectorAll("[data-modal-body]").forEach(function (el) {
      el.hidden = el.getAttribute("data-modal-body") !== productKey;
    });
    detailsModal.hidden = false;
    document.body.style.overflow = "hidden";
  }

  function closeDetailsModal() {
    if (!detailsModal) return;
    detailsModal.hidden = true;
    document.body.style.overflow = "";
  }

  wizard.addEventListener("click", function (e) {
    var toggle = e.target.closest(".ttq-js-toggle-details");
    if (toggle) {
      e.preventDefault();
      openDetailsModal(toggle.getAttribute("data-product-key"));
      return;
    }
    if (e.target.closest(".ttq-js-modal-close")) {
      closeDetailsModal();
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && detailsModal && !detailsModal.hidden) {
      closeDetailsModal();
    }
  });

  /* ── Step 2: quantity stepper ───────────────────────────────── */
  wizard.addEventListener("click", function (e) {
    var qtyInput = form.querySelector('[name="quantity"]');
    if (!qtyInput) {
      return;
    }
    var min = parseInt(qtyInput.min, 10) || 1;
    var max = parseInt(qtyInput.max, 10) || 999999;

    if (e.target.closest(".ttq-js-qty-inc")) {
      if (qtyInput.value === "") {
        qtyInput.value = min;
      } else {
        qtyInput.value = Math.min(max, (parseInt(qtyInput.value, 10) || 0) + 1);
      }
    }
    if (e.target.closest(".ttq-js-qty-dec")) {
      if (qtyInput.value === "") {
        return;
      }
      qtyInput.value = Math.max(min, (parseInt(qtyInput.value, 10) || 0) - 1);
    }
  });

  /* Character counters */
  function updateCharCounters() {
    wizard.querySelectorAll(".ttq-js-char-counter").forEach(function (input) {
      var wrap = input.closest("div") || input.parentElement;
      var counter = wrap ? wrap.querySelector(".ttq-js-char-count") : null;
      if (counter) {
        counter.textContent = input.value.length;
      }
    });
  }
  wizard.addEventListener("input", function (e) {
    if (e.target.classList.contains("ttq-js-char-counter")) {
      updateCharCounters();
    }
  });

  /* ── Logo upload ────────────────────────────────────────────── */
  var dropzone = wizard.querySelector(".ttq-js-dropzone");
  var fileInput = wizard.querySelector(".ttq-js-file-input");
  var progressEl = wizard.querySelector(".ttq-js-upload-progress");
  var progressFill = wizard.querySelector(".ttq-js-progress-fill");
  var progressLabel = wizard.querySelector(".ttq-js-progress-label");
  var previewEl = wizard.querySelector(".ttq-js-upload-preview");
  var previewImg = wizard.querySelector(".ttq-js-preview-img");
  var previewName = wizard.querySelector(".ttq-js-preview-name");
  var tokenInput = wizard.querySelector(".ttq-js-logo-token");

  function showUploadPreview(url, name) {
    if (previewImg) {
      previewImg.src = url;
    }
    if (previewName) {
      previewName.textContent = name;
    }
    if (previewEl) {
      previewEl.hidden = false;
    }
    if (dropzone) {
      dropzone.hidden = true;
    }
  }

  function resetUpload() {
    state.logoToken = "";
    state.logoPreviewUrl = "";
    state.logoFileName = "";
    if (tokenInput) {
      tokenInput.value = "";
    }
    if (previewEl) {
      previewEl.hidden = true;
    }
    if (dropzone) {
      dropzone.hidden = false;
    }
    if (fileInput) {
      fileInput.value = "";
    }
  }

  function uploadLogo(file) {
    var errEl = wizard.querySelector('[data-error-for="logo"]');
    if (errEl) {
      errEl.textContent = "";
    }

    var body = new FormData();
    body.append("action", "ttq_upload_logo");
    body.append("nonce", TTQ_DATA.nonce);
    body.append("logo", file);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", TTQ_DATA.ajaxUrl, true);
    if (progressEl) {
      progressEl.hidden = false;
    }

    xhr.upload.onprogress = function (evt) {
      if (!evt.lengthComputable) {
        return;
      }
      var pct = Math.round((evt.loaded / evt.total) * 100);
      if (progressFill) {
        progressFill.style.width = pct + "%";
      }
      if (progressLabel) {
        progressLabel.textContent = pct + "%";
      }
    };

    xhr.onload = function () {
      if (progressEl) {
        progressEl.hidden = true;
      }
      var res;
      try {
        res = JSON.parse(xhr.responseText);
      } catch (e) {
        res = null;
      }
      if (res && res.success) {
        state.logoToken = res.data.token;
        state.logoPreviewUrl = res.data.previewUrl;
        state.logoFileName = res.data.fileName;
        if (tokenInput) {
          tokenInput.value = state.logoToken;
        }
        showUploadPreview(state.logoPreviewUrl, state.logoFileName);
        saveState();
      } else if (errEl) {
        errEl.textContent =
          res && res.data && res.data.message
            ? res.data.message
            : TTQ_DATA.i18n.genericError;
      }
    };

    xhr.onerror = function () {
      if (progressEl) {
        progressEl.hidden = true;
      }
      if (errEl) {
        errEl.textContent = TTQ_DATA.i18n.genericError;
      }
    };

    xhr.send(body);
  }

  if (dropzone) {
    dropzone.addEventListener("click", function () {
      fileInput.click();
    });
    dropzone.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        fileInput.click();
      }
    });
    ["dragenter", "dragover"].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) {
        e.preventDefault();
        dropzone.classList.add("is-dragover");
      });
    });
    ["dragleave", "drop"].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) {
        e.preventDefault();
        dropzone.classList.remove("is-dragover");
      });
    });
    dropzone.addEventListener("drop", function (e) {
      var file = e.dataTransfer.files && e.dataTransfer.files[0];
      if (file) {
        uploadLogo(file);
      }
    });
  }
  if (fileInput) {
    fileInput.addEventListener("change", function () {
      if (fileInput.files && fileInput.files[0]) {
        uploadLogo(fileInput.files[0]);
      }
    });
  }
  wizard.addEventListener("click", function (e) {
    if (e.target.closest(".ttq-js-upload-remove")) {
      resetUpload();
    }
    if (e.target.closest(".ttq-js-upload-replace")) {
      resetUpload();
      fileInput.click();
    }
  });

  /* ── Review population ──────────────────────────────────────── */
  function labelFor(list, key) {
    var match = list.filter(function (item) {
      return item.key === key;
    })[0];
    return match ? match.label : key;
  }

  function productFor(key) {
    return (
      (TTQ_DATA.products || []).filter(function (p) {
        return p.key === key;
      })[0] || null
    );
  }

  function populateReview() {
    readFormIntoState();

    var product = productFor(state.product);
    var productLabel = product ? product.label : state.product;

    setReview("product", productLabel);
    setReview("quantity", state.quantity + " units");
    setReview(
      "sizes",
      state.sizes
        .map(function (s) {
          return labelFor(TTQ_DATA.sizes, s);
        })
        .join(", ") || "—",
    );
    var displayColors = state.colors.map(function (c) {
      return labelFor(TTQ_DATA.colors, c);
    });
    if (state.custom_color) {
      displayColors.push(state.custom_color + " (Custom)");
    }
    setReview("colors", displayColors.join(", ") || "—");
    setReview("side1", state.side1 || "—");
    setReview("side2", state.side2 || "—");
    setReview("name", state.name);
    setReview("organization", state.organization || "—");
    setReview("phone", state.phone);
    setReview("email", state.email);
    setReview("free_sample", state.free_sample === "yes" ? "Yes" : "No");
    setReview("comments", state.comments || "—");

    var isKit = state.product === "complete_kit";
    var materialLabels = { fabric: "Fabric / Canvas", synthetic_leather: "Synthetic Leather" };
    setReview("material", isKit ? materialLabels[state.material] || "—" : "—");
    setReview("carabiner_clip", isKit ? (state.carabiner_clip === "yes" ? "Yes" : "No") : "—");
    var materialRow = wizard.querySelector(".ttq-js-review-material-row");
    var clipRow = wizard.querySelector(".ttq-js-review-clip-row");
    if (materialRow) materialRow.style.display = isKit ? "" : "none";
    if (clipRow) clipRow.style.display = isKit ? "" : "none";

    // Address only shown when a free sample was requested
    var addressWrap = wizard.querySelector(".ttq-js-review-address-wrap");
    var noAddress = wizard.querySelector(".ttq-js-review-no-address");
    if (state.free_sample === "yes") {
      setReview("address", state.address || "—");
      if (addressWrap) addressWrap.hidden = false;
      if (noAddress) noAddress.hidden = true;
    } else {
      if (addressWrap) addressWrap.hidden = true;
      if (noAddress) noAddress.hidden = false;
    }

    // Product thumbnail in review
    var thumb = wizard.querySelector(".ttq-js-review-product-img");
    var nameEl = wizard.querySelector(".ttq-js-review-product-name");
    var tagEl = wizard.querySelector(".ttq-js-review-product-tag");

    if (thumb) {
      if (product && product.image) {
        thumb.src = product.image;
        thumb.style.display = "block";
      } else {
        thumb.style.display = "none";
      }
    }
    if (nameEl) {
      nameEl.textContent = productLabel;
    }
    if (tagEl && product) {
      var featured =
        product.featured && product.featured !== "0" && product.featured !== "";
      tagEl.textContent = featured
        ? "★ Most Popular"
        : product.key === "tweezers_only"
          ? "Tick Tweezers Only"
          : "";
    }

    // Logo
    var logoImg = wizard.querySelector(".ttq-js-review-logo");
    var noLogo = wizard.querySelector(".ttq-js-review-no-logo");
    if (state.logoToken && state.logoPreviewUrl) {
      if (logoImg) {
        logoImg.src = state.logoPreviewUrl;
        logoImg.style.display = "block";
      }
      if (noLogo) {
        noLogo.style.display = "none";
      }
    } else {
      if (logoImg) {
        logoImg.style.display = "none";
      }
      if (noLogo) {
        noLogo.style.display = "block";
      }
    }
  }

  function setReview(field, value) {
    var el = wizard.querySelector('[data-review="' + field + '"]');
    if (el) {
      el.textContent = value;
    }
  }

  /* ── Final submission ───────────────────────────────────────── */
  function showOverlay(which) {
    overlay.hidden = false;
    ["submitting", "success", "error"].forEach(function (name) {
      var panel = wizard.querySelector(".ttq-js-overlay-" + name);
      if (panel) {
        panel.hidden = name !== which;
      }
    });
  }
  function hideOverlay() {
    overlay.hidden = true;
  }

  wizard.addEventListener("click", function (e) {
    if (e.target.closest(".ttq-js-overlay-close")) {
      var successPanel = wizard.querySelector(".ttq-js-overlay-success");
      var isSuccess = successPanel && !successPanel.hidden;
      
      hideOverlay();
      
      if (isSuccess) {
        if (TTQ_DATA.redirectUrl) {
          window.location.href = TTQ_DATA.redirectUrl;
        } else {
          window.location.reload();
        }
      }
    }

    if (e.target.closest(".ttq-js-submit")) {
      readFormIntoState();
      showOverlay("submitting");
      
      // Set to 5 so the progress bar reaches 100% and all steps turn green
      wizard.setAttribute("data-step", "5");
      updateTrack(5);

      var body = new FormData();
      body.append("action", "ttq_submit_quote");
      body.append("nonce", TTQ_DATA.nonce);
      body.append("logo_token", state.logoToken);

      appendGroup(body, "step1", { product: state.product });
      appendGroup(body, "step2", {
        quantity: state.quantity,
        colors: state.colors,
        sizes: state.sizes,
        side1: state.side1,
        side2: state.side2,
        custom_color: state.custom_color,
        material: state.product === "complete_kit" ? state.material : "",
        carabiner_clip:
          state.product === "complete_kit" ? state.carabiner_clip : "",
        comments: state.comments,
      });
      appendGroup(body, "step3", {
        organization: state.organization,
        name: state.name,
        phone: state.phone,
        email: state.email,
        address: state.address,
        free_sample: state.free_sample,
      });

      var started = Date.now();
      var MIN_MS = 900;

      fetch(TTQ_DATA.ajaxUrl, {
        method: "POST",
        body: body,
        credentials: "same-origin",
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          var elapsed = Date.now() - started;
          var wait = Math.max(0, MIN_MS - elapsed);
          setTimeout(function () {
            if (res.success) {
              var msgEl = wizard.querySelector(
                ".ttq-js-overlay-success-message",
              );
              if (msgEl && res.data && res.data.message) {
                msgEl.textContent = res.data.message;
              }
              showOverlay("success");
              
              // Reset JS state silently in background
              state.product = "";
              state.quantity = "";
              state.colors = [];
              state.sizes = [];
              state.side1 = "";
              state.side2 = "";
              state.organization = "";
              state.name = "";
              state.phone = "";
              state.email = "";
              state.free_sample = "no";
              state.address = "";
              state.logoToken = "";
              state.logoPreviewUrl = "";
              state.logoFileName = "";
              state.custom_color = "";
              state.material = "fabric";
              state.carabiner_clip = "no";
              state.comments = "";
              
              // Clear session storage so returning user gets fresh form
              try {
                sessionStorage.removeItem(STORAGE_KEY);
              } catch (e) {}
              
              // Reset all form inputs in background (user stays on popup)
              if (form) {
                form.reset();
              }
              resetUpload();
              updateCharCounters();
            } else {
              var errMsgEl = wizard.querySelector(
                ".ttq-js-overlay-error-message",
              );
              var message =
                res.data && res.data.message
                  ? res.data.message
                  : TTQ_DATA.i18n.genericError;
              if (errMsgEl) {
                errMsgEl.textContent = message;
              }
              showOverlay("error");
              wizard.setAttribute("data-step", "4");
              if (res.data && res.data.errors) {
                showErrors(currentPanel(), res.data.errors);
              }
            }
          }, wait);
        })
        .catch(function () {
          showOverlay("error");
          wizard.setAttribute("data-step", "4");
          var errMsgEl = wizard.querySelector(".ttq-js-overlay-error-message");
          if (errMsgEl) {
            errMsgEl.textContent = TTQ_DATA.i18n.genericError;
          }
        });
    }
  });

  function appendGroup(body, group, obj) {
    Object.keys(obj).forEach(function (key) {
      var val = obj[key];
      if (Array.isArray(val)) {
        val.forEach(function (v) {
          body.append(group + "[" + key + "][]", v);
        });
      } else {
        body.append(group + "[" + key + "]", val);
      }
    });
  }

  /* ── Init ───────────────────────────────────────────────────── */
  loadState();
  applyStateToForm();
  readFormIntoState(); // pick up the server-rendered default-checked product radio
  updateProductUI();
  updateFreeSampleUI();
  goToStep(currentStep);

  // Keep state fresh as user types
  form.addEventListener("input", function () {
    readFormIntoState();
    saveState();
  });
  form.addEventListener("change", function () {
    readFormIntoState();
    saveState();
  });
  form.addEventListener("submit", function (e) {
    e.preventDefault();
  });
})();
