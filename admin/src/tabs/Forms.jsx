import { useState, useEffect, useCallback } from '@wordpress/element';
import { Button, TextControl, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ToggleRow from '../components/ToggleRow';
import { fetchSubmissions, deleteSubmission, fetchMailchimpLists, getExportUrl } from '../api';

export default function Forms( { settings, onChange } ) {
	const [ submissions, setSubmissions ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ page, setPage ] = useState( 1 );
	const [ total, setTotal ] = useState( 0 );
	const [ pages, setPages ] = useState( 0 );
	const [ search, setSearch ] = useState( '' );
	const [ mcLists, setMcLists ] = useState( [] );
	const [ mcLoading, setMcLoading ] = useState( false );
	const [ expanded, setExpanded ] = useState( null );

	const loadSubmissions = useCallback( async () => {
		setLoading( true );
		try {
			const data = await fetchSubmissions( page, 20, search );
			setSubmissions( data.items );
			setTotal( data.total );
			setPages( data.pages );
		} catch ( err ) {
			// eslint-disable-next-line no-console
			console.error( err );
		}
		setLoading( false );
	}, [ page, search ] );

	useEffect( () => {
		loadSubmissions();
	}, [ loadSubmissions ] );

	const handleDelete = async ( id ) => {
		if ( ! window.confirm( __( 'Delete this submission?', 'brndle' ) ) ) return;
		await deleteSubmission( id );
		loadSubmissions();
	};

	const loadMcLists = async () => {
		setMcLoading( true );
		try {
			const data = await fetchMailchimpLists();
			setMcLists( data.lists || [] );
		} catch ( err ) {
			// eslint-disable-next-line no-console
			console.error( err );
		}
		setMcLoading( false );
	};

	useEffect( () => {
		if ( settings.mailchimp_api_key ) {
			loadMcLists();
		}
	}, [ settings.mailchimp_api_key ] );

	return (
		<div className="brndle-forms">
			<h3 className="brndle-section-title">{ __( 'Integrations', 'brndle' ) }</h3>

			<TextControl
				label={ __( 'Mailchimp API Key', 'brndle' ) }
				type="password"
				value={ settings.mailchimp_api_key || '' }
				onChange={ ( v ) => onChange( 'mailchimp_api_key', v ) }
				help={ __( 'Find in Mailchimp > Account > Extras > API Keys', 'brndle' ) }
			/>

			{ settings.mailchimp_api_key && (
				<div style={ { marginBottom: '1rem' } }>
					<label style={ { display: 'block', fontWeight: 600, marginBottom: 4, fontSize: 13 } }>
						{ __( 'Mailchimp List', 'brndle' ) }
					</label>
					{ mcLoading ? (
						<Spinner />
					) : (
						<>
							<select
								value={ settings.mailchimp_list_id || '' }
								onChange={ ( e ) => onChange( 'mailchimp_list_id', e.target.value ) }
								style={ { width: '100%', padding: '8px', borderRadius: 4, border: '1px solid #ccc' } }
							>
								<option value="">{ __( '-- Select a list --', 'brndle' ) }</option>
								{ mcLists.map( ( list ) => (
									<option key={ list.id } value={ list.id }>
										{ list.name } ({ list.member_count } members)
									</option>
								) ) }
							</select>
							<Button
								variant="link"
								onClick={ loadMcLists }
								style={ { marginTop: 4, fontSize: 12 } }
							>
								{ __( 'Refresh lists', 'brndle' ) }
							</Button>
						</>
					) }
				</div>
			) }

			<TextControl
				label={ __( 'Webhook URL', 'brndle' ) }
				value={ settings.form_webhook_url || '' }
				onChange={ ( v ) => onChange( 'form_webhook_url', v ) }
				help={ __( 'Forward all submissions as JSON POST to this URL (Zapier, Make, n8n, etc.)', 'brndle' ) }
			/>

			<TextControl
				label={ __( 'Notification Email', 'brndle' ) }
				type="email"
				value={ settings.form_notification_email || '' }
				onChange={ ( v ) => onChange( 'form_notification_email', v ) }
				help={ __( 'Receive email when a form is submitted. Defaults to admin email if empty.', 'brndle' ) }
			/>

			<ToggleRow
				label={ __( 'Store Submissions', 'brndle' ) }
				description={ __( 'Save all form submissions in the database', 'brndle' ) }
				checked={ !! settings.form_store_submissions }
				onChange={ ( v ) => onChange( 'form_store_submissions', v ) }
			/>

			<ToggleRow
				label={ __( 'Email Notifications', 'brndle' ) }
				description={ __( 'Send admin email on each new submission', 'brndle' ) }
				checked={ !! settings.form_email_notifications }
				onChange={ ( v ) => onChange( 'form_email_notifications', v ) }
			/>

			<div style={ { marginTop: '2rem', paddingTop: '1.5rem', borderTop: '1px solid #e0e0e0' } }>
				<h3 className="brndle-section-title">{ __( 'Submissions', 'brndle' ) }</h3>

				<div style={ { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem', gap: '1rem' } }>
					<TextControl
						placeholder={ __( 'Search by email...', 'brndle' ) }
						value={ search }
						onChange={ ( v ) => { setSearch( v ); setPage( 1 ); } }
						__nextHasNoMarginBottom
					/>
					<div style={ { display: 'flex', gap: 8, flexShrink: 0 } }>
						<Button
							variant="secondary"
							href={ getExportUrl() }
							target="_blank"
							disabled={ total === 0 }
						>
							{ __( 'Export CSV', 'brndle' ) }
						</Button>
						<Button variant="tertiary" onClick={ loadSubmissions }>
							{ __( 'Refresh', 'brndle' ) }
						</Button>
					</div>
				</div>

				{ loading ? (
					<div style={ { textAlign: 'center', padding: '2rem' } }><Spinner /></div>
				) : submissions.length === 0 ? (
					<p style={ { color: '#757575', textAlign: 'center', padding: '2rem' } }>
						{ __( 'No submissions yet. Add a Lead Form block to any page to start collecting.', 'brndle' ) }
					</p>
				) : (
					<>
						<table style={ { width: '100%', borderCollapse: 'collapse', fontSize: 13 } }>
							<thead>
								<tr style={ { borderBottom: '2px solid #e0e0e0' } }>
									<th style={ { textAlign: 'left', padding: '8px 12px', fontWeight: 600 } }>{ __( 'Date', 'brndle' ) }</th>
									<th style={ { textAlign: 'left', padding: '8px 12px', fontWeight: 600 } }>{ __( 'Email', 'brndle' ) }</th>
									<th style={ { textAlign: 'left', padding: '8px 12px', fontWeight: 600 } }>{ __( 'Source', 'brndle' ) }</th>
									<th style={ { textAlign: 'center', padding: '8px 12px', fontWeight: 600 } }>{ __( 'MC', 'brndle' ) }</th>
									<th style={ { textAlign: 'right', padding: '8px 12px', fontWeight: 600 } }>{ __( 'Actions', 'brndle' ) }</th>
								</tr>
							</thead>
							<tbody>
								{ submissions.map( ( sub ) => (
									<>
										<tr key={ sub.id } style={ { borderBottom: '1px solid #f0f0f0' } }>
											<td style={ { padding: '8px 12px' } }>{ sub.date }</td>
											<td style={ { padding: '8px 12px', fontWeight: 500 } }>{ sub.email || '—' }</td>
											<td style={ { padding: '8px 12px', color: '#757575', maxWidth: 200, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' } }>
												{ sub.source ? new URL( sub.source ).pathname : '—' }
											</td>
											<td style={ { padding: '8px 12px', textAlign: 'center' } }>
												<span style={ { fontSize: 11, padding: '2px 6px', borderRadius: 4, background: sub.mailchimp === 'synced' ? '#dcfce7' : sub.mailchimp === 'failed' ? '#fef2f2' : '#f5f5f5', color: sub.mailchimp === 'synced' ? '#166534' : sub.mailchimp === 'failed' ? '#991b1b' : '#737373' } }>
													{ sub.mailchimp }
												</span>
											</td>
											<td style={ { padding: '8px 12px', textAlign: 'right' } }>
												<Button variant="link" isSmall onClick={ () => setExpanded( expanded === sub.id ? null : sub.id ) }>
													{ expanded === sub.id ? __( 'Close', 'brndle' ) : __( 'View', 'brndle' ) }
												</Button>
												<Button variant="link" isDestructive isSmall onClick={ () => handleDelete( sub.id ) } style={ { marginLeft: 8 } }>
													{ __( 'Delete', 'brndle' ) }
												</Button>
											</td>
										</tr>
										{ expanded === sub.id && (
											<tr key={ sub.id + '-detail' } style={ { background: '#fafafa' } }>
												<td colSpan="5" style={ { padding: '12px 24px' } }>
													{ Object.entries( sub.fields ).map( ( [ k, v ] ) => (
														<div key={ k } style={ { marginBottom: 4 } }>
															<strong style={ { color: '#555' } }>{ k }:</strong> { v }
														</div>
													) ) }
												</td>
											</tr>
										) }
									</>
								) ) }
							</tbody>
						</table>

						{ pages > 1 && (
							<div style={ { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '1rem', fontSize: 13 } }>
								<span style={ { color: '#757575' } }>
									{ total } { __( 'total submissions', 'brndle' ) }
								</span>
								<div style={ { display: 'flex', gap: 4 } }>
									<Button variant="secondary" isSmall disabled={ page <= 1 } onClick={ () => setPage( page - 1 ) }>
										&larr;
									</Button>
									<span style={ { padding: '4px 8px', lineHeight: '28px' } }>
										{ page } / { pages }
									</span>
									<Button variant="secondary" isSmall disabled={ page >= pages } onClick={ () => setPage( page + 1 ) }>
										&rarr;
									</Button>
								</div>
							</div>
						) }
					</>
				) }
			</div>
		</div>
	);
}
