import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	SelectControl,
	Button,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType( 'brndle/team', {
	icon: (
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
			<circle cx="8" cy="7" r="3" />
			<circle cx="16" cy="7" r="3" />
			<path strokeLinecap="round" d="M2 20c0-3.314 2.686-6 6-6s6 2.686 6 6" />
			<path strokeLinecap="round" d="M16 14c2 0 6 1 6 6" />
		</svg>
	),

	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const members = attributes.members || [];

		const updateMember = ( index, key, value ) => {
			const newMembers = [ ...members ];
			newMembers[ index ] = { ...newMembers[ index ], [ key ]: value };
			setAttributes( { members: newMembers } );
		};

		const addMember = () => {
			setAttributes( {
				members: [
					...members,
					{
						name: '',
						role: '',
						bio: '',
						photo: '',
						linkedin: '',
						twitter: '',
					},
				],
			} );
		};

		const removeMember = ( index ) => {
			setAttributes( {
				members: members.filter( ( _, i ) => i !== index ),
			} );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section Header', 'brndle' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Eyebrow', 'brndle' ) }
							value={ attributes.eyebrow }
							onChange={ ( v ) =>
								setAttributes( { eyebrow: v } )
							}
						/>
						<TextareaControl
							label={ __( 'Title', 'brndle' ) }
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help={ __( 'Supports HTML for styling', 'brndle' ) }
						/>
						<TextareaControl
							label={ __( 'Subtitle', 'brndle' ) }
							value={ attributes.subtitle }
							onChange={ ( v ) =>
								setAttributes( { subtitle: v } )
							}
						/>
						<SelectControl
							label={ __( 'Columns', 'brndle' ) }
							value={ attributes.columns }
							options={ [
								{ label: __( '2 Columns', 'brndle' ), value: '2' },
								{ label: __( '3 Columns', 'brndle' ), value: '3' },
								{ label: __( '4 Columns', 'brndle' ), value: '4' },
							] }
							onChange={ ( v ) =>
								setAttributes( { columns: v } )
							}
						/>
						<SelectControl
							label={ __( 'Variant', 'brndle' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Light', 'brndle' ), value: 'light' },
								{ label: __( 'Dark', 'brndle' ), value: 'dark' },
							] }
							onChange={ ( v ) => setAttributes( { variant: v } ) }
						/>
					</PanelBody>

					{ members.map( ( member, i ) => (
						<PanelBody
							key={ i }
							title={ `Member ${ i + 1 }${
								member.name ? `: ${ member.name }` : ''
							}` }
							initialOpen={ false }
						>
							<TextControl
								label={ __( 'Name', 'brndle' ) }
								value={ member.name }
								onChange={ ( v ) =>
									updateMember( i, 'name', v )
								}
							/>
							<TextControl
								label={ __( 'Role', 'brndle' ) }
								value={ member.role }
								onChange={ ( v ) =>
									updateMember( i, 'role', v )
								}
							/>
							<TextareaControl
								label={ __( 'Bio', 'brndle' ) }
								value={ member.bio }
								onChange={ ( v ) =>
									updateMember( i, 'bio', v )
								}
							/>
							<TextControl
								label={ __( 'Photo URL', 'brndle' ) }
								value={ member.photo }
								onChange={ ( v ) =>
									updateMember( i, 'photo', v )
								}
							/>
							<TextControl
								label={ __( 'LinkedIn URL', 'brndle' ) }
								value={ member.linkedin }
								onChange={ ( v ) =>
									updateMember( i, 'linkedin', v )
								}
							/>
							<TextControl
								label={ __( 'X / Twitter URL', 'brndle' ) }
								value={ member.twitter }
								onChange={ ( v ) =>
									updateMember( i, 'twitter', v )
								}
							/>
							<Button
								isDestructive
								isSmall
								onClick={ () => removeMember( i ) }
							>
								{ __( 'Remove Member', 'brndle' ) }
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title={ __( 'Add Member', 'brndle' ) } initialOpen={ true }>
						<Button variant="secondary" onClick={ addMember }>
							{ __( 'Add Team Member', 'brndle' ) }
						</Button>
					</PanelBody>
				</InspectorControls>

				<div { ...blockProps }>
					<ServerSideRender
						block="brndle/team"
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},

	save: () => null,
} );
