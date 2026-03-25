import { registerBlockType } from '@wordpress/blocks';
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
					<PanelBody title="Section Header" initialOpen={ true }>
						<TextControl
							label="Eyebrow"
							value={ attributes.eyebrow }
							onChange={ ( v ) =>
								setAttributes( { eyebrow: v } )
							}
						/>
						<TextareaControl
							label="Title"
							value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							help="Supports HTML for styling"
						/>
						<TextareaControl
							label="Subtitle"
							value={ attributes.subtitle }
							onChange={ ( v ) =>
								setAttributes( { subtitle: v } )
							}
						/>
						<SelectControl
							label="Columns"
							value={ attributes.columns }
							options={ [
								{ label: '2 Columns', value: '2' },
								{ label: '3 Columns', value: '3' },
								{ label: '4 Columns', value: '4' },
							] }
							onChange={ ( v ) =>
								setAttributes( { columns: v } )
							}
						/>
						<SelectControl
							label="Variant"
							value={ attributes.variant }
							options={ [
								{ label: 'Light', value: 'light' },
								{ label: 'Dark', value: 'dark' },
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
								label="Name"
								value={ member.name }
								onChange={ ( v ) =>
									updateMember( i, 'name', v )
								}
							/>
							<TextControl
								label="Role"
								value={ member.role }
								onChange={ ( v ) =>
									updateMember( i, 'role', v )
								}
							/>
							<TextareaControl
								label="Bio"
								value={ member.bio }
								onChange={ ( v ) =>
									updateMember( i, 'bio', v )
								}
							/>
							<TextControl
								label="Photo URL"
								value={ member.photo }
								onChange={ ( v ) =>
									updateMember( i, 'photo', v )
								}
							/>
							<TextControl
								label="LinkedIn URL"
								value={ member.linkedin }
								onChange={ ( v ) =>
									updateMember( i, 'linkedin', v )
								}
							/>
							<TextControl
								label="X / Twitter URL"
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
								Remove Member
							</Button>
						</PanelBody>
					) ) }

					<PanelBody title="Add Member" initialOpen={ true }>
						<Button variant="secondary" onClick={ addMember }>
							Add Team Member
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
