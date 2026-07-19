/**
 * TickTweezers Admin — Repeater Row Builder
 *
 * Builds dynamic repeater rows for Products, Colors, and Sizes on the
 * Quote Settings page. Serializes each repeater to a hidden JSON input
 * on form submit so sanitize_settings() can parse it server-side.
 *
 * Fields handled specially:
 *   hex       → <input type="color">
 *   featured  → <input type="checkbox">
 *   features  → <textarea> (pipe-separated bullets)
 *   image     → WP Media Library picker with preview
 *   colors    → checklist from data-all-colors
 *   sizes     → checklist from data-all-sizes
 */
( function () {
	'use strict';

	/* ── helpers ──────────────────────────────────────────────── */

	function labelText( field ) {
		return field.replace( /_/g, ' ' ).replace( /\b\w/g, function ( c ) { return c.toUpperCase(); } );
	}

	function makeLabel( text ) {
		var el = document.createElement( 'span' );
		el.textContent = text;
		el.style.cssText = 'display:block;font-size:11px;font-weight:600;margin-bottom:4px;color:#50575e;';
		return el;
	}

	/* ── field builders ───────────────────────────────────────── */

	function buildColorField( field, value ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'ttq-repeater__field-wrap';
		wrap.appendChild( makeLabel( labelText( field ) ) );

		var input = document.createElement( 'input' );
		input.type = 'color';
		input.dataset.field = field;
		input.value = value || '#cccccc';
		wrap.appendChild( input );
		return wrap;
	}

	function buildFeaturedField( field, value ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'ttq-repeater__field-wrap';
		wrap.appendChild( makeLabel( labelText( field ) ) );

		var lbl = document.createElement( 'label' );
		lbl.style.cssText = 'display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;font-weight:normal;';

		var cb = document.createElement( 'input' );
		cb.type = 'checkbox';
		cb.dataset.field = field;
		cb.dataset.isCheckbox = '1';
		cb.value = '1';
		if ( value && value !== '' && value !== '0' && value !== 'false' ) {
			cb.checked = true;
		}
		lbl.appendChild( cb );
		lbl.appendChild( document.createTextNode( 'Mark as Most Popular / Featured' ) );
		wrap.appendChild( lbl );
		return wrap;
	}

	function buildFeaturesField( field, value ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'ttq-repeater__field-wrap';
		wrap.appendChild( makeLabel( labelText( field ) ) );

		var ta = document.createElement( 'textarea' );
		ta.dataset.field = field;
		ta.placeholder = 'Feature 1|Feature 2|Feature 3';
		ta.value = value || '';
		ta.rows = 3;
		ta.style.cssText = 'width:100%;font-size:12px;resize:vertical;';
		wrap.appendChild( ta );

		var hint = document.createElement( 'p' );
		hint.style.cssText = 'font-size:11px;color:#888;margin:3px 0 0;';
		hint.textContent = 'Separate bullet points with | (pipe)';
		wrap.appendChild( hint );
		return wrap;
	}

	function buildImageField( field, value ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'ttq-repeater__field-wrap';
		wrap.appendChild( makeLabel( 'Product Image' ) );

		var imgWrap = document.createElement( 'div' );
		imgWrap.className = 'ttq-repeater__img-wrap';

		var preview = document.createElement( 'img' );
		preview.className = 'ttq-repeater__img-preview';
		if ( value ) { preview.src = value; }
		else { preview.style.display = 'none'; }
		imgWrap.appendChild( preview );

		var urlInput = document.createElement( 'input' );
		urlInput.type = 'text';
		urlInput.dataset.field = field;
		urlInput.placeholder = 'Image URL';
		urlInput.value = value || '';
		urlInput.style.cssText = 'font-size:12px;width:100%;margin-bottom:4px;';
		urlInput.addEventListener( 'input', function () {
			if ( urlInput.value ) {
				preview.src = urlInput.value;
				preview.style.display = 'block';
			} else {
				preview.style.display = 'none';
			}
		} );
		imgWrap.appendChild( urlInput );

		// WP Media Library button (only if wp.media is available)
		if ( window.wp && window.wp.media ) {
			var uploadBtn = document.createElement( 'button' );
			uploadBtn.type = 'button';
			uploadBtn.className = 'button button-secondary';
			uploadBtn.textContent = '📷 Upload / Select Image';
			uploadBtn.style.cssText = 'font-size:11px;margin-bottom:4px;';

			var frame;
			uploadBtn.addEventListener( 'click', function () {
				if ( frame ) { frame.open(); return; }
				frame = wp.media( {
					title: 'Select Product Image',
					button: { text: 'Use this image' },
					multiple: false
				} );
				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					urlInput.value = attachment.url;
					preview.src = attachment.url;
					preview.style.display = 'block';
				} );
				frame.open();
			} );
			imgWrap.appendChild( uploadBtn );
		}

		var removeBtn = document.createElement( 'button' );
		removeBtn.type = 'button';
		removeBtn.className = 'ttq-repeater__img-remove';
		removeBtn.textContent = '✕ Remove Image';
		removeBtn.addEventListener( 'click', function () {
			urlInput.value = '';
			preview.src = '';
			preview.style.display = 'none';
		} );
		imgWrap.appendChild( removeBtn );

		wrap.appendChild( imgWrap );
		return wrap;
	}

	function buildChecklistField( field, value, allItems ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'ttq-repeater__field-wrap';
		wrap.appendChild( makeLabel( labelText( field ) ) );

		var checklist = document.createElement( 'div' );
		checklist.className = 'ttq-repeater__checklist';
		checklist.dataset.field = field;

		var currentVals = ( value || '' ).split( ',' ).map( function ( v ) { return v.trim(); } ).filter( Boolean );

		allItems.forEach( function ( item ) {
			var lbl = document.createElement( 'label' );
			var cb = document.createElement( 'input' );
			cb.type = 'checkbox';
			cb.value = item.key;
			if ( currentVals.indexOf( item.key ) !== -1 ) { cb.checked = true; }
			lbl.appendChild( cb );

			// For colors, show a swatch dot
			if ( item.hex ) {
				var swatch = document.createElement( 'span' );
				swatch.style.cssText = 'display:inline-block;width:12px;height:12px;border-radius:50%;background:' + item.hex + ';border:1px solid #ccc;flex-shrink:0;';
				lbl.appendChild( swatch );
			}
			lbl.appendChild( document.createTextNode( ' ' + item.label ) );
			checklist.appendChild( lbl );
		} );

		wrap.appendChild( checklist );
		return wrap;
	}

	function buildTextField( field, value ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'ttq-repeater__field-wrap';
		wrap.appendChild( makeLabel( labelText( field ) ) );

		var input = document.createElement( 'input' );
		input.type = 'text';
		input.dataset.field = field;
		input.placeholder = labelText( field );
		input.value = value || '';
		wrap.appendChild( input );
		return wrap;
	}

	/* ── row builder ──────────────────────────────────────────── */

	function buildRow( fields, values, container ) {
		values = values || {};
		var allColors = container.allColors || [];
		var allSizes  = container.allSizes  || [];

		var row = document.createElement( 'div' );
		row.className = 'ttq-repeater__row';

		fields.forEach( function ( field ) {
			var el;
			switch ( field ) {
				case 'hex':      el = buildColorField( field, values[field] ); break;
				case 'featured': el = buildFeaturedField( field, values[field] ); break;
				case 'features': el = buildFeaturesField( field, values[field] ); break;
				case 'image':    el = buildImageField( field, values[field] ); break;
				case 'colors':   el = buildChecklistField( field, values[field], allColors ); break;
				case 'sizes':    el = buildChecklistField( field, values[field], allSizes ); break;
				default:         el = buildTextField( field, values[field] ); break;
			}
			row.appendChild( el );
		} );

		var removeBtn = document.createElement( 'button' );
		removeBtn.type = 'button';
		removeBtn.className = 'ttq-repeater__remove';
		removeBtn.innerHTML = '&times;';
		removeBtn.title = 'Remove row';
		removeBtn.addEventListener( 'click', function () { row.remove(); } );
		row.appendChild( removeBtn );

		return row;
	}

	/* ── serializer ───────────────────────────────────────────── */

	function serializeRows( list ) {
		return Array.prototype.map.call( list.children, function ( row ) {
			var obj = {};

			// Normal inputs and textareas
			Array.prototype.forEach.call( row.querySelectorAll( '[data-field]' ), function ( el ) {
				if ( el.classList.contains( 'ttq-repeater__checklist' ) ) { return; } // handled below
				if ( el.dataset.isCheckbox ) {
					obj[ el.dataset.field ] = el.checked ? '1' : '';
				} else {
					obj[ el.dataset.field ] = el.value;
				}
			} );

			// Checklists (colors / sizes per product)
			Array.prototype.forEach.call( row.querySelectorAll( '.ttq-repeater__checklist[data-field]' ), function ( checklist ) {
				var checked = Array.prototype.map.call(
					checklist.querySelectorAll( 'input[type="checkbox"]:checked' ),
					function ( cb ) { return cb.value; }
				);
				obj[ checklist.dataset.field ] = checked.join( ',' );
			} );

			return obj;
		} );
	}

	/* ── init ─────────────────────────────────────────────────── */

	function initRepeater( container ) {
		var fields = container.dataset.fields ? container.dataset.fields.split( ',' ) : [];
		var rows   = [];
		var allColors = [];
		var allSizes  = [];

		try { rows      = JSON.parse( container.dataset.rows     || '[]' ); } catch (e) { rows = []; }
		try { allColors = JSON.parse( container.dataset.allColors || '[]' ); } catch (e) { allColors = []; }
		try { allSizes  = JSON.parse( container.dataset.allSizes  || '[]' ); } catch (e) { allSizes  = []; }

		container.allColors = allColors;
		container.allSizes  = allSizes;

		var list = document.createElement( 'div' );
		list.className = 'ttq-repeater__list';
		container.appendChild( list );

		rows.forEach( function ( rowData ) { list.appendChild( buildRow( fields, rowData, container ) ); } );

		var addBtn = document.createElement( 'button' );
		addBtn.type = 'button';
		addBtn.className = 'button ttq-repeater__add';
		addBtn.textContent = '+ Add Row';
		addBtn.addEventListener( 'click', function () { list.appendChild( buildRow( fields, {}, container ) ); } );
		container.appendChild( addBtn );

		// Serialize to hidden input on form submit
		var hiddenInput = document.getElementById( container.id + '-input' );
		var form = container.closest( 'form' );
		if ( form && hiddenInput ) {
			form.addEventListener( 'submit', function () {
				hiddenInput.value = JSON.stringify( serializeRows( list ) );
			} );
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.ttq-repeater' ).forEach( initRepeater );

		// Single CPT Product Edit Media Picker
		var uploadBtn = document.getElementById( 'ttq-upload-btn' );
		var removeLink = document.getElementById( 'ttq-remove-img' );
		var urlInput   = document.getElementById( 'ttq-image-url' );
		var preview    = document.getElementById( 'ttq-preview-img' );

		if ( uploadBtn && urlInput && preview ) {
			var frame;
			uploadBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( frame ) { frame.open(); return; }
				frame = wp.media( {
					title: 'Select Product Image',
					button: { text: 'Use this image' },
					multiple: false
				} );
				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					urlInput.value = attachment.url;
					preview.src = attachment.url;
					preview.style.display = 'block';
				} );
				frame.open();
			} );
			if ( removeLink ) {
				removeLink.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					urlInput.value = '';
					preview.src = '';
					preview.style.display = 'none';
				} );
			}
		}
	} );

} )();
